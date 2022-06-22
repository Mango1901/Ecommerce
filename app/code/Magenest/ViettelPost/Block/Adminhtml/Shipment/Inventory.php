<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\ViettelPost\Block\Adminhtml\Shipment;

use Magenest\ViettelPost\Helper\Data;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Display selected source on shipment creation page
 *
 * @api
 */
class Inventory extends Template
{
    const PROVINCE_DATA_CACHE_ID = 'PROVINCE_COLLECTION_DATA';
    const DISTRICT_DATA_CACHE_ID = 'DISTRICT_COLLECTION_DATA';
    const WARDS_DATA_CACHE_ID = 'WARDS_COLLECTION_DATA';

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    private $helper;

    protected $provinceCollectionFactory;

    protected $districtCollectionFactory;

    protected $wardsCollectionFactory;

    /**
     * Inventory constructor.
     * @param Context $context
     * @param Registry $registry
     * @param SourceRepositoryInterface $sourceRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        SourceRepositoryInterface $sourceRepository,
        \Magenest\ViettelPost\Model\ResourceModel\Province\CollectionFactory $provinceCollectionFactory,
        \Magenest\ViettelPost\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        \Magenest\ViettelPost\Model\ResourceModel\Wards\CollectionFactory $wardsCollectionFactory,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->sourceRepository = $sourceRepository;
        $this->helper = $helper;
        $this->provinceCollectionFactory = $provinceCollectionFactory;
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->wardsCollectionFactory = $wardsCollectionFactory;
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Shipment
     */
    public function getShipment()
    {
        return $this->registry->registry('current_shipment');
    }

    /**
     * Retrieve source code from shipment
     *
     * @return null|string
     */
    public function getSourceCode()
    {
        $shipment = $this->getShipment();
        $extensionAttributes = $shipment->getExtensionAttributes();
        if ($sourceCode = $extensionAttributes->getSourceCode()) {
            return $sourceCode;
        }
        return null;
    }

    /**
     * Get source name by code
     *
     * @param $sourceCode
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSourceName(string $sourceCode): string
    {
        return $this->sourceRepository->get($sourceCode)->getName();
    }

    public function isDisplayed(){
        $order = $this->getShipment()->getOrder();
        return $this->helper->isUsedBackendShipping($order);
    }

    public function getOrderId(){
        return $this->getShipment()->getOrderId();
    }

    public function getShippingCarrier(){
        return $this->helper->getBackendCarrierList();
    }

    public function getCarrierCode(){
        return 'viettel_post';
    }

    /**
     * @param $shippingCode
     * @return \Magenest\ViettelPost\Model\ShippingCarrierInterface
     */
    public function getCarrierModel($shippingCode){
        return $this->helper->getCarrierModel($shippingCode);
    }

    public function getProvinceData(){
        $data = $this->_cache->load(self::PROVINCE_DATA_CACHE_ID);
        if(!$data){
            $dataDB = $this->provinceCollectionFactory->create()
                ->addFieldToSelect(["province_id","province_name"])
                ->getData();
            $data = $dataDB;
            $this->_cache->save(json_encode($dataDB), self::PROVINCE_DATA_CACHE_ID);
        }else{
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getDistrictData(){
        $data = $this->_cache->load(self::DISTRICT_DATA_CACHE_ID);
        if(!$data){
            $dataDB = $this->districtCollectionFactory->create()
                ->addFieldToSelect(["district_id","province_id","district_name"])
                ->getData();
            $data = $dataDB;
            $this->_cache->save(json_encode($dataDB), self::DISTRICT_DATA_CACHE_ID);
        }else{
            $data = json_decode($data, true);
        }
        return $data;
    }

    public function getWardsData(){
        $data = $this->_cache->load(self::WARDS_DATA_CACHE_ID);
        if(!$data){
            $dataDB = $this->wardsCollectionFactory->create()
                ->addFieldToSelect(["wards_id","district_id", "wards_name"])
                ->getData();
            $data = $dataDB;
            $this->_cache->save(json_encode($dataDB), self::WARDS_DATA_CACHE_ID);
        }else{
            $data = json_decode($data, true);
        }
        return $data;
    }
}
