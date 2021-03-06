<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Definition;
use Swagger\Annotations\Items;
use Swagger\Context;
use Swagger\Analysis;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 */
class AugmentProperties
{
    public static $types = array(
        'array' => 'array',
        'byte' => array('string', 'byte'),
        'boolean' => 'boolean',
        'bool' => 'boolean',
        'int' => 'integer',
        'integer' => 'integer',
        'long' => array('integer', 'long'),
        'float' => array('number', 'float'),
        'double' => array('number', 'double'),
        'string' => 'string',
        'date' => array('string', 'date'),
        'datetime' => array('string', 'date-time'),
        '\datetime' => array('string', 'date-time'),
        'datetimeimmutable' => array('string', 'date-time'),
        '\datetimeimmutable' => array('string', 'date-time'),
        'datetimeinterface' => array('string', 'date-time'),
        '\datetimeinterface' => array('string', 'date-time'),
        'number' => 'number',
        'object' => 'object'
    );

    public function __invoke(Analysis $analysis)
    {
        $refs = array();
        /** @var Definition $definition */
        foreach ($analysis->swagger->definitions as $definition) {
            if ($definition->definition) {
                $refs[strtolower($definition->_context->fullyQualifiedName($definition->_context->class))] = '#/definitions/' . $definition->definition;
            }
        }
        
        $allProperties = $analysis->getAnnotationsOfType('\Swagger\Annotations\Property');
        /** @var \Swagger\Annotations\Property $property */
        foreach ($allProperties as $property) {
            $context = $property->_context;
            // Use the property names for @SWG\Property()
            if ($property->property === null) {
                $property->property = $context->property;
            }

            if (preg_match('/@var\s+(?<type>[^\s]+)([ \t])?(?<description>.+)?$/im', $context->comment, $varMatches)) {
                if ($property->description === null && isset($varMatches['description'])) {
                    $property->description = trim($varMatches['description']);
                }
                if ($property->type === null) {
                    preg_match('/^([^\[]+)(.*$)/', trim($varMatches['type']), $typeMatches);
                    $type = $typeMatches[1];

                    if (array_key_exists(strtolower($type), static::$types)) {
                        $type = static::$types[strtolower($type)];
                        if (is_array($type)) {
                            if ($property->format === null) {
                                $property->format = $type[1];
                            }
                            $type = $type[0];
                        }
                        $property->type = $type;
                    } elseif ($property->ref === null && $typeMatches[2] === '') {
                        $tmpKey = strtolower($context->fullyQualifiedName($type));
                        $property->ref = array_key_exists($tmpKey, $refs) ? $refs[$tmpKey] : null;
                    }
                    if ($typeMatches[2] === '[]') {
                        if ($property->items === null) {
                            $property->items = new Items(
                                array(
                                'type' => $property->type,
                                '_context' => new Context(array('generated' => true), $context)
                                )
                            );
                            if ($property->items->type === null) {
                                $tmpKey = strtolower($context->fullyQualifiedName($type));
                                $property->items->ref = array_key_exists($tmpKey, $refs) ? $refs[$tmpKey] : null;
                            }
                        }
                        $property->type = 'array';
                    }
                }
            }
            if ($property->description === null) {
                $property->description = $context->phpdocContent();
            }
        }
    }
}
