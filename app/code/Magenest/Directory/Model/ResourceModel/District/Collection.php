<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model\ResourceModel\District;

use Magenest\Directory\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\Directory\Model\ResourceModel\City
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'district_id';

    /**
     * {@inheritdoc}
     */
    protected $_foreignKey = 'city_id';

    /**
     * {@inheritdoc}
     */
    protected $_defaultOptionLabel = 'Please select district';

    /**
     * {@inheritdoc}
     */
    protected $_sortable = true;
    /**
     * @var \Magenest\Directory\Model\ResourceModel\City
     */
    private $cityResource;

    public function __construct(
        \Magenest\Directory\Model\ResourceModel\City $cityResource,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->cityResource = $cityResource;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Magenest\Directory\Model\District::class, \Magenest\Directory\Model\ResourceModel\District::class);
    }

    public function storefrontToOptionArray()
    {
        $arr = parent::toOptionArray();
        $disabledCities = $this->cityResource->fetchAllDisableOnStoreFront();
        foreach ($arr as $key => $value) {
            $disabled = !empty($value['disable_on_storefront']) ? (bool)$value['disable_on_storefront'] : false;
            if ($disabled) {
                unset($arr[$key]);
                continue;
            }
            if (!empty($disabledCities) && !empty($value['city_id']) && in_array($value['city_id'], $disabledCities)) {
                unset($arr[$key]);
            }
        }
        return $arr;
    }
}
