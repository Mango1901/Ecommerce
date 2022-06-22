<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */
namespace Magenest\OrderManagement\Ui\Component\DataProvider;

use Magento\Framework\Api\Filter;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Zend_Db_Expr;

class OrderDataProvider extends DataProvider
{
    public function addFilter(Filter $filter)
    {
        /** @var ResourceConnection $resource */
        $resource = ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
        if ($filter->getField() == 'fulltext') {
            $so = $resource->getTableName('sales_order_grid');
            $soa = $resource->getTableName('sales_order_address');
            $strFirst = str_replace(', ', ' ', $filter->getValue());
            $stringValue = str_replace(' ', '|', str_replace(',', ' ', $strFirst));

            $connection = $resource->getConnection();
            $select = $connection->select()->from(['main_table' => $so], 'entity_id');
            $select->joinLeft(
                ['soa' => $soa],
                '`main_table`.`entity_id` = `soa`.`parent_id` AND `soa`.`address_type` = \'shipping\'',
                []
            );
            $select->where(
                '`soa`.`telephone` regexp :telephone
                OR `main_table`.`increment_id` regexp :increment_id
                OR `main_table`.`shipping_name` regexp :billing_name
                OR `main_table`.`billing_name` regexp :billing_name'
            );
            $orderIds = $connection->fetchCol($select, [
                ':telephone' => $filter->getValue(),
                ':increment_id' => $stringValue,
                ':billing_name' => $filter->getValue(),
            ]);
            $this->addFilter(
                $this->filterBuilder->setField('entity_id')->setValue(implode(",", $orderIds))->setConditionType('in')->create()
            );
        } elseif ($filter->getField() == 'skus') {
            $soi = $resource->getTableName('sales_order_item');
            $sql = new Zend_Db_Expr(
                "(SELECT `{$soi}`.`order_id` FROM `{$soi}`WHERE `{$soi}`.`sku` like '%{$filter->getValue()}%' GROUP BY `{$soi}`.`order_id`)"
            );
            $connection = $resource->getConnection();
            $orderIds = $connection->fetchCol($sql);
            $this->addFilter(
                $this->filterBuilder->setField('entity_id')->setValue(implode(",", $orderIds))->setConditionType('in')->create()
            );
        } else {
            parent::addFilter($filter);
        }
    }

    public function getData()
    {
        $data = parent::getData();

        foreach ($data['items'] as &$item) {
            $item['skus'] = preg_replace("/ --- /i", "</br>", $item['skus']);
        }

        return $data;
    }
}
