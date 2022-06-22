<?php
/**
 * Collection
 *
 * @copyright Copyright © 2021 Khanh. All rights reserved.
 * @author    khanhthanhvh@gmail.com
 */

namespace Magenest\Report\Model\ResourceModel\Order;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magenest\Report\Model\ResourceModel\Order;
use Magento\Sales\Model\Order\Address;

class Collection extends AbstractCollection
{

    public function getTotalCount()
    {
        return $this->getSize();
    }

    protected function _construct()
    {
        $this->_init(
            Document::class,
            Order::class
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();
        $tableDescription = $this->getConnection()->describeTable($this->getMainTable());

        foreach ($tableDescription as $columnInfo) {
            $this->addFilterToMap($columnInfo['COLUMN_NAME'], 'main_table.' . $columnInfo['COLUMN_NAME']);
        }
        $this->joinTable();
        return $this;
    }


    public function joinTable()
    {
        $this->getSelect()->joinLeft(
            ['so' => $this->getTable('sales_order')],
            'main_table.entity_id = so.entity_id',
            [
                'customer_lastname' => 'so.customer_lastname',
                'customer_firstname' => 'so.customer_firstname',
                'shipping_amount' => 'so.shipping_amount',
                'tax_code' => "replace(replace(`so`.`tax_code`,' ',''),'\t','')",
            ]
            )->joinLeft(
                ['soa'=> $this->getTable('sales_order_address')],
                "main_table.entity_id = soa.parent_id AND soa.address_type = '" . Address::TYPE_BILLING . "'",
                [
                    "telephone" => "replace(`soa`.`telephone`,' ','')",
                    'street' => "soa.street",
                    'district_ward' => new \Zend_Db_Expr(sprintf('CONCAT(%s,\', \',%s)',
                            new \Zend_Db_Expr('soa.ward'),
                            new \Zend_Db_Expr("soa.district")
                        )
                    ),
                    'city' => "soa.city"
                ]
            )->joinLeft(
             ['order_item' => $this->getTable('sales_order_item')],
             'main_table.entity_id = order_item.order_id',
                [
                    'item_id' => 'order_item.item_id',
                    'sku' => 'order_item.sku',
                    'qty_ordered' => 'order_item.qty_ordered',
                ]
            );

        return $this;
    }
}


