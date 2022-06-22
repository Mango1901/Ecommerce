<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Preview
 * @package Magenest\MegaMenu\Model\ResourceModel
 */
class Preview extends AbstractDb
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('magenest_menu_preview', 'entity_id');
    }

    /**
     * @inheritdoc
     */
    protected function _beforeSave(AbstractModel $object)
    {
        parent::_beforeSave($object);
        $oldData = \Zend_Json::decode($object->getData('data'));
        $pattern = '/[\[\]]/u';
        $newData = [];

        foreach ($oldData as $value) {
            $keys = preg_split($pattern, $value['name']);
            $newData = $this->arrayMergeRecursiveDistinct($newData, $this->convert($keys, $value['value']));
        }

        if (isset($newData['menu'])) {
            foreach ($newData['menu'] as $id => $item) {
                $item = array_merge($item, \Zend_Json::decode($item['data']));
                $newData['menu'][$id] = $item;
                if ($item['itemEnable'] == 0){
                    unset($newData['menu'][$id]);
                } else {
                    if ($item['children'] != '0' || $item['mainContentHtml'] != '') {
                        $newData['menu'][$id]['mainEnable'] = '1';
                    } else {
                        $newData['menu'][$id]['mainEnable'] = '0';
                    }
                }
            }

            if (isset($newData['remove_items'])) {
                $removeItems = explode(',', $newData['remove_items']);

                foreach ($removeItems as $item) {
                    unset($newData['menu'][$item]);
                }
            }
        }

        $object->setData('data', \Zend_Json::encode($newData));

        return $this;
    }

    /**
     * Merge multidimensional array
     *
     * @param array $array1
     * @param array $array2
     * @return array
     */
    function arrayMergeRecursiveDistinct(array $array1, array $array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = $this->arrayMergeRecursiveDistinct($merged [$key], $value);
            } else {
                $merged [$key] = $value;
            }
        }

        return $merged;
    }

    /**
     * Convert keys to multidimensional array
     *
     * @param string $keys
     * @param mixed $value end value
     * @return array
     */
    public function convert($keys, $value)
    {
        $result = [];

        if ($keys != null) {
            $key = $keys[0];
            array_shift($keys);

            if ($key == '') {
                $result = $this->convert($keys, $value);
            } else {
                $result[$key] = $this->convert($keys, $value);
            }
        } else {
            $result = $value;
        }

        return $result;
    }
}
