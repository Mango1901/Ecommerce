<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model;

use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Framework\DataObject;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Psr\Log\LoggerInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magenest\CustomTableRate\Model\ResourceModel\CarrierFactory;
use Magenest\Core\Helper\Data as CoreData;

/**
 * Class Standard
 * @package Magenest\CustomTableRate\Model
 */
abstract class Carrier extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @var CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var CoreData
     */
    protected $_coreData;

    /**
     * {@inheritdoc}
     */
    protected $_isFixed = true;

    /**
     * @var null|array
     */
    protected $_rateRecord = null;

    /**
     * Constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param CarrierFactory $carrierFactory
     * @param CoreData $coreData
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        CarrierFactory $carrierFactory,
        CoreData $coreData,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->_carrierFactory = $carrierFactory;
        $this->_coreData = $coreData;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();
        $rate = $this->getRate($request);

        if (!empty($rate) && $rate['fee'] >= 0) {
            $originalFee = $rate['fee'];
            if ($request->getFreeShipping()) {
                $rate['fee'] = 0;
            }
            // TODO: CHANGE SOURCE CODE FROM QUOTE REQUEST
            $method = $this->_createCustomTableRateMethod($rate['fee']);
            $method->setOriginalPrice($originalFee);
            $result->append($method);
        }

        return $result;
    }

    /**
     * Get rate
     *
     * @param $request
     * @return array|bool
     */
    public function getRate($request)
    {
        if (!$this->_rateRecord) {
            $request->setMethod($this->_code);
            $this->_rateRecord = $this->_carrierFactory->create()->getRate($request);
        }

        return $this->_rateRecord;
    }

    /**
     * Get the method object based on the shipping price and cost
     *
     * @param float $fee
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function _createCustomTableRateMethod($fee)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($fee);
        $method->setCost($fee);

        return $method;
    }

    /**
     * {@inheritdoc}
     */
    public function checkAvailableShipCountries(DataObject $request)
    {
        $rate = $this->getRate($request);
        if (empty($rate) || $rate['fee'] < 0) {
            return false;
        }

        return parent::checkAvailableShipCountries($request);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('title')];
    }

    /**
     * Get surcharge config
     *
     * @param $field
     * @return false|string
     */
    private function _getSurchargeConfig($field)
    {
        return $this->getConfigData('surcharge/' . $field);
    }
}
