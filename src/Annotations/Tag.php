<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 *
 * A Swagger "Tag Object":  https://github.com/swagger-api/swagger-spec/blob/master/versions/2.0.md#tagObject
 */
class Tag extends AbstractAnnotation
{
    /**
     * The name of the tag.
     * @var string
     */
    public $name;

    /**
     * A short description for the tag. GFM syntax can be used for rich text representation.
     * @var string
     */
    public $description;

    /**
     * Additional external documentation for this tag.
     * @var ExternalDocumentation
     */
    public $externalDocs;

    /** @inheritdoc */
    public static $_required = array('name');

    /** @inheritdoc */
    public static $_types = array(
        'name' => 'string',
        'description' => 'string',
    );

    /** @inheritdoc */
    public static $_parents = array(
        'Swagger\Annotations\Swagger'
    );

    /** @inheritdoc */
    public static $_nested = array(
        'Swagger\Annotations\ExternalDocumentation' => 'externalDocs'
    );
}
