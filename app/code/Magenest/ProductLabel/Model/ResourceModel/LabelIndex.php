<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Model\ResourceModel;


class LabelIndex
{
    /**
     * Cached resources singleton
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resources;

    /**
     * Prefix for resources that will be used in this resource model
     *
     * @var string
     */
    protected $connectionName = \Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION;

    /**
     * LabelIndex constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        $connectionName = null
    ) {
        $this->_resources = $context->getResources();
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|false
     */
    public function getConnection()
    {
        $fullResourceName = ($this->connectionName ? $this->connectionName : ResourceConnection::DEFAULT_CONNECTION);
        return $this->_resources->getConnection($fullResourceName);
    }

    /**
     * Get main table resource
     *
     * @return string
     */
    public function getMainTable()
    {
        return $this->_resources->getTableName('magenest_product_label_product');
    }

    /**
     * @param $productId
     * @param $storeId
     * @param $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabelIds($productId, $storeId, $customerGroupId)
    {
        $ids = $this->getConnection()->fetchCol(
            $this->getConnection()
                ->select()
                ->from($this->getMainTable(), ['label_id'])
                ->where('store_id = ?', $storeId)
                ->where('customer_group_id = ?', $customerGroupId)
                ->where('product_id = ?', $productId)
        );

        return $ids;
    }

    /**
     * @return array
     */
    public function getAllProductIds()
    {
        return $this->getConnection()->fetchCol(
            $this->getConnection()
                ->select()
                ->from($this->getMainTable(), ['product_id'])
                ->distinct()
        );
    }

    /**
     * @param array $productId
     */
    public function deleteIndexProduct(array $productId) {
        $this->getConnection()->delete($this->getMainTable(), ['product_id in (?)' => $productId]);
    }

    /**
     * @param string $labelId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductIdsByLabelId($labelId)
    {
        if ($labelId) {
            return $this->getConnection()->fetchCol(
                $this->getConnection()
                    ->select()
                    ->from($this->getMainTable(), ['product_id'])
                    ->where('label_id = ?', $labelId)
                    ->distinct()
            );
        }
    }

    /**
     * @param $labelIds
     * @return array
     */
    public function getProductIdsByLabelIds($labelIds)
    {
        if ($labelIds) {
            return $this->getConnection()->fetchCol(
                $this->getConnection()
                        ->select()
                        ->from($this->getMainTable(), ['product_id'])
                        ->where('label_id IN(?)', $labelIds)
                        ->distinct()
            );
        }
    }

    /**
     * @param $labelsIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanByLabelIds($labelsIds)
    {
        $query = $this->getConnection()->deleteFromSelect(
            $this->getConnection()
                ->select()
                ->from($this->getMainTable(), 'label_id')
                ->where('label_id' . ' IN (?)', $labelsIds),
            $this->getMainTable()
        );

        $this->getConnection()->query($query);
    }

    /**
     * @param $productIds
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanByProductIds($productIds)
    {
        $query = $this->getConnection()->deleteFromSelect(
            $this->getConnection()
                ->select()
                ->from($this->getMainTable(), 'label_id')
                ->where('product_id' . ' IN (?)', $productIds),
            $this->getMainTable()
        );

        $this->getConnection()->query($query);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function cleanIndexFull()
    {
        $this->getConnection()->delete($this->getMainTable());
    }
}
