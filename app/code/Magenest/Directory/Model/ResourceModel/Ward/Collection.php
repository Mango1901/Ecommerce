<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Directory\Model\ResourceModel\Ward;

use Magenest\Directory\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @package Magenest\Directory\Model\ResourceModel\Ward
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = 'ward_id';

    /**
     * {@inheritdoc}
     */
    protected $_foreignKey = 'district_id';

    /**
     * {@inheritdoc}
     */
    protected $_defaultOptionLabel = 'Please select ward';

    /**
     * {@inheritdoc}
     */
    protected $_sortable = true;
    /**
     * @var \Magenest\Directory\Model\ResourceModel\District
     */
    private $districtResource;

    public function __construct(
        \Magenest\Directory\Model\ResourceModel\District $districtResource,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->districtResource = $districtResource;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Magenest\Directory\Model\Ward::class, \Magenest\Directory\Model\ResourceModel\Ward::class);
    }

    public function storefrontToOptionArray()
    {
        $arr = parent::toOptionArray();
        $disabledDistricts = $this->districtResource->fetchAllDisableOnStoreFront();
        foreach ($arr as $key => $value) {
            $disabled = !empty($value['disable_on_storefront']) ? (bool)$value['disable_on_storefront'] : false;
            if ($disabled) {
                unset($arr[$key]);
                continue;
            }
            if (!empty($disabledDistricts) && !empty($value['district_id']) && in_array($value['district_id'], $disabledDistricts)) {
                unset($arr[$key]);
            }
        }
        return $arr;
    }
}
