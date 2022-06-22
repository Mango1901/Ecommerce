<?php
/**
 * Created by PhpStorm.
 * User: ducquach
 * Date: 3/9/21
 * Time: 1:31 PM
 */
namespace Magenest\ElasticsearchVi\Model;

/**
 * Class AccentAttribute
 * @package Magenest\ElasticsearchVi\Model
 */
class AccentAttribute
{
    const NON_ACCENT_KEY = 'non_accent';

    /**
     * @var array
     */
    private $attributeToFilterWithAccent = [];

    /**
     * AccentAttribute constructor.
     * @param array $attributeToFilterWithAccent
     */
    public function __construct(
        $attributeToFilterWithAccent = []
    ) {
        $this->attributeToFilterWithAccent = $attributeToFilterWithAccent;
    }

    /**
     * @param $attributeName
     * @return bool
     */
    public function isAttributeFilterableWithAccent($attributeName)
    {
        return !empty($this->attributeToFilterWithAccent[$attributeName]);
    }

    /**
     * @return array
     */
    public function getFilterableWithAccentAttributes()
    {
        return $this->attributeToFilterWithAccent;
    }
}