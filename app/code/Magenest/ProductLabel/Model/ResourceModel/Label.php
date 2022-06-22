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

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Indexer\IndexerRegistry;

/**
 * Class Label
 * @package Magenest\ProductLabel\Model\ResourceModel
 */
class Label extends AbstractDb
{

    /**
     * @var IndexerRegistry
     */
    protected $indexerRegistry;

    /**
     * Label constructor.
     * @param Context $context
     * @param IndexerRegistry $indexerRegistry
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        IndexerRegistry $indexerRegistry,
        $connectionName = null
    )
    {
        $this->indexerRegistry = $indexerRegistry;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('magenest_product_label', 'label_id');
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return Label
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->lookupStoreIds($object->getId());
            $object->setData('store_id', $stores);

            $customers = $this->lookupCustomerIds($object->getId());
            $object->setData('customer_groups_ids', $customers);

            $categoryData = $this->lookupCategoryData($object->getId());
            $object->setData('category_data', $categoryData);

            $productData = $this->lookupProductData($object->getId());
            $object->setData('product_data', $productData);
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return Label
     */
    protected function _afterDelete(\Magento\Framework\Model\AbstractModel $object)
    {
        $connection = $this->getConnection();
        $connection->delete(
            $this->getTable('magenest_product_label_store'),
            ['label_id=?' => $object->getId()]
        );
        $connection->delete(
            $this->getTable('magenest_product_label_customer_group'),
            ['label_id=?' => $object->getId()]
        );
        $connection->delete(
            $this->getTable('magenest_product_label_option_category'),
            ['label_id=?' => $object->getId()]
        );
        $connection->delete(
            $this->getTable('magenest_product_label_option_product'),
            ['label_id=?' => $object->getId()]
        );

        return parent::_afterDelete($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return Label
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $oldStores = $this->lookupStoreIds($object->getId());
        $newStores = (array)$object->getStoreId();
        $this->updateForeignKey($object, $oldStores, $newStores, 'magenest_product_label_store', 'store_id');

        $oldCustomer = $this->lookupCustomerIds($object->getId());
        $newCustomer = (array)$object->getCustomerGroupsIds();
        $this->updateForeignKey($object, $oldCustomer, $newCustomer, 'magenest_product_label_customer_group', 'customer_group_id');

        $categoryData = (array)$object->getCategoryData();
        $categoryData['label_id'] = $object->getId();
        if(!$this->lookupCategoryData($object->getId())) {
            $this->getConnection()->insert($this->getTable('magenest_product_label_option_category'), $categoryData);
        } else $this->getConnection()->update($this->getTable('magenest_product_label_option_category'), $categoryData, ['label_id = ?' => $object->getId()]);

        $productData = (array)$object->getProductData();
        $productData['label_id'] = $object->getId();
        if(!$this->lookupProductData($object->getId())) {
            $this->getConnection()->insert($this->getTable('magenest_product_label_option_product'), $productData);
        } else $this->getConnection()->update($this->getTable('magenest_product_label_option_product'), $productData, ['label_id = ?' => $object->getId()]);

        //Indexer Label
        $indexer = $this->indexerRegistry->get('product_label');
        $indexer->reindexRow($object->getId());
        return parent::_afterSave($object);
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param array $oldRelatedIds
     * @param array $newRelatedIds
     * @param $tableName
     * @param $field
     */
    public function updateForeignKey(
        \Magento\Framework\Model\AbstractModel $object,
        array $oldRelatedIds,
        array $newRelatedIds,
        $tableName,
        $field
    ) {
        $table  = $this->getTable($tableName);
        $insert = array_diff($newRelatedIds, $oldRelatedIds);
        $delete = array_diff($oldRelatedIds, $newRelatedIds);

        if ($delete) {
            $where = ['label_id = ?' => (int)$object->getId(), $field . ' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }
        if ($insert) {
            $data = [];
            foreach ($insert as $relatedId) {
                $data[] = ['label_id' => (int)$object->getId(), $field => (int)$relatedId];
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * @param $id
     * @return array
     */
    public function lookupCategoryData($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('magenest_product_label_option_category'),
            '*'
        )->where(
            'label_id = ?', (int)$id
        );

        return $connection->fetchRow($select);
    }

    /**
     * @param $id
     * @return array
     */
    public function lookupProductData($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('magenest_product_label_option_product'),
            '*'
        )->where(
            'label_id = ?', (int)$id
        );

        return $connection->fetchRow($select);
    }

    /**
     * @param $id
     * @return array
     */
    public function lookupStoreIds($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('magenest_product_label_store'),
            'store_id'
        )->where(
            'label_id = :label_id'
        );
        $binds = [':label_id' => (int)$id];

        return $connection->fetchCol($select, $binds);
    }

    /**
     * @param $id
     * @return array
     */
    public function lookupCustomerIds($id)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('magenest_product_label_customer_group'),
            'customer_group_id'
        )->where(
            'label_id = :label_id'
        );
        $binds = [':label_id' => (int)$id];

        return $connection->fetchCol($select, $binds);
    }
}
