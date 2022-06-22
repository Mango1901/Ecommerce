<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model\ResourceModel;

/**
 * Class City
 * @package Magenest\Directory\Model\ResourceModel
 */
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    const FIELD_DISTRICT = 'district_id';
    const FIELD_WARD = 'ward_id';

    /**
     * Foreign key
     */
    protected $_foreignKey = null;

    /**
     * @var string
     */
    protected $_defaultOptionLabel = '';

    /**
     * @var string
     */
    protected $_defaultValue = '';

    /**
     * @var string
     */
    protected $_label = 'default_name';

    /**
     * @var bool
     */
    protected $_sortable = false;

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = $this->prepareOptionArray();
        array_unshift($options, ['value' => '', 'label' => __($this->_defaultOptionLabel), $this->_foreignKey => $this->_defaultValue]);

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArrayCustom($id = null)
    {
        $options = $this->prepareOptionArrayCustom($id);
        array_unshift($options, ['value' => '', 'label' => __($this->_defaultOptionLabel), $this->_foreignKey => $this->_defaultValue]);

        return $options;
    }

    /**
     * Prepare option array
     *
     * @return array
     */
    public function prepareOptionArray()
    {
        $options = [];
        if  (!empty($this->getItems())) {
            foreach ($this->getItems() as $item) {
                $data = [];
                foreach (
                    [
                        'value' => $this->getIdFieldName(),
                        'label' => $this->_label,
                        'name' => 'name',
                        'full_name' => 'default_name',
                        'disable_on_storefront' => 'disable_on_storefront',
                        $this->_foreignKey => $this->_foreignKey,
                    ]
                    as $code => $field) {
                    $data[$code] = $item->getData($field);
                }

                $options[] = $data;
            }

            if ($this->_sortable) {
                usort($options, function ($first, $second) {
                    return ($first['name'] <= $second['name']) ? -1 : 1;
                });
            }
        }
        return $options;
    }

    /**
     * Prepare option array
     *
     * @return array
     */
    public function prepareOptionArrayCustom($id = null)
    {
        $options = [];
        if  (!empty($this->getItems())) {
            foreach ($this->getItems() as $item) {
                $data = [];
                $fieldName = $this->getIdFieldName();
                foreach (
                    [
                        'value' => $this->getIdFieldName(),
                        'label' => $this->_label,
                        'name' => 'name',
                        'full_name' => 'default_name',
                        'disable_on_storefront' => 'disable_on_storefront',
                        $this->_foreignKey => $this->_foreignKey,
                    ]
                    as $code => $field) {
                    $data[$code] = $item->getData($field);
                }
                if($fieldName == self::FIELD_DISTRICT){
                    if($data['city_id'] == $id) {
                        $options[] = $data;
                    }
                }
                elseif($fieldName == self::FIELD_WARD){
                    if($data['district_id'] == $id) {
                        $options[] = $data;
                    }
                }
                else{
                    $options[] = $data;
                }
            }

            if ($this->_sortable) {
                usort($options, function ($first, $second) {
                    return ($first['name'] <= $second['name']) ? -1 : 1;
                });
            }
        }
        return $options;
    }

    /**
     * Set label
     *
     * @param $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->_label = $label;

        return $this;
    }

    /**
     * Get foreign eky
     *
     * @return null|string
     */
    public function getForeignKey()
    {
        return $this->_foreignKey;
    }
}
