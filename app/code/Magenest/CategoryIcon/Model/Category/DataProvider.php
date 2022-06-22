<?php


namespace Magenest\CategoryIcon\Model\Category;

/**
 * Class DataProvider
 * @package Magenest\CategoryIcon\Model\Category
 */
class DataProvider extends \Magento\Catalog\Model\Category\DataProvider
{
    /**
     * @return array
     */
    protected function getFieldsMap()
    {
        $fields              = parent::getFieldsMap();
        $fields['content'][] = 'category_icon';
        return $fields;
    }
}