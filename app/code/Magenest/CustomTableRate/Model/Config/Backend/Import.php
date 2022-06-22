<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magenest\CustomTableRate\Model\ResourceModel\CarrierFactory;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Import
 * @package Magenest\CustomTableRate\Model\Config\Backend
 */
class Import extends Value
{
    /**
     * @var CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param CarrierFactory $carrierFactory
     * @param string $method
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        CarrierFactory $carrierFactory,
        $method = '',
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_carrierFactory = $carrierFactory;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function afterSave()
    {
        /** @var \Magenest\CustomTableRate\Model\ResourceModel\Carrier $carrier */
        $carrier = $this->_carrierFactory->create();
        $carrier->uploadAndImport($this);

        return parent::afterSave();
    }
}
