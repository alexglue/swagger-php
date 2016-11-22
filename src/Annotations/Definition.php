<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Definition extends Schema
{
    /**
     * The key into Swagger->definitions array.
     * @var string
     */
    public $definition;

    /** @inheritdoc */
    public static $_types = array(
        'definition' => 'string'
    );

    /** @inheritdoc */
    public static $_parents = array(
        'Swagger\Annotations\Swagger'
    );
}
