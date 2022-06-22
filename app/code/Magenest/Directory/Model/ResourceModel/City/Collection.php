<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\ResourceModel\City;

use Magenest\Directory\Model\ResourceModel\AbstractCollection;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Collection
 * @package Magenest\Directory\Model\ResourceModel\City
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'city_id';

    /**
     * {@inheritdoc}
     */
    protected $_foreignKey = 'country_id';

    /**
     * {@inheritdoc}
     */
    protected $_defaultOptionLabel = 'Please select city';

    /**
     * {@inheritdoc}
     */
    protected $_defaultValue = 'VN';

    /**
     * {@inheritdoc}
     */
    protected $_sortable = true;

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Magenest\Directory\Model\City::class, \Magenest\Directory\Model\ResourceModel\City::class);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareOptionArray()
    {
        $options = parent::prepareOptionArray();

        /**
         * Move raised cities up top option
         */
        $options['58']['name'] = "3-".$options['58']['name'];
        array_unshift($options, $options[58]); // Đà Nẵng
        $options[25]['name'] = "2-".$options[25]['name'];
        array_unshift($options, $options[25]); // Hồ Chí Minh
        $options[20]['name'] = "1-".$options[20]['name'];
        array_unshift($options, $options[20]); // Hà Nội

        unset($options[21]);
        unset($options[27]);
        unset($options[61]);
        return $options;
    }

    public function storefrontToOptionArray()
    {
        $arr = parent::toOptionArray();
       foreach ($arr as $key => $value) {
           $disabled = !empty($value['disable_on_storefront']) ? (bool)$value['disable_on_storefront'] : false;
           if ($disabled) {
               unset($arr[$key]);
           }
       }
       return $arr;
    }
}
