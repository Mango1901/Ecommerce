<?php

namespace Magenest\Customer\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class DefaultAddress extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;
    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magenest\Directory\Model\ResourceModel\Ward\CollectionFactory
     */
    protected $wardCollectionFactory;
    /**
     * @var \Magenest\Directory\Model\ResourceModel\District\CollectionFactory
     */
    protected $districtCollectionFactory;

    /**
     * DefaultAddress constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magenest\Directory\Model\ResourceModel\Ward\CollectionFactory $wardCollectionFactory
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magenest\Directory\Model\ResourceModel\Ward\CollectionFactory $wardCollectionFactory,
        \Magenest\Directory\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->wardCollectionFactory = $wardCollectionFactory;
        $this->_customerFactory = $customerFactory;
        $this->_addressFactory  = $addressFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $customer = $this->_customerFactory->create()->load($item["entity_id"]);
                $shippingAddressId = $customer->getData("default_shipping");
                if (!empty($shippingAddressId) && $shippingAddressId != "0") {
                    $shippingAddress = $this->_addressFactory->create()->load($shippingAddressId);
                    if (isset($shippingAddress)) {
                        $item["city_id"] = $shippingAddress->getCity();
                        $item["street"] = $shippingAddress->getStreetFull();
                        if ($shippingAddress->getDistrictId() !== null) {
                            $item["district"] = $this->getDistrict($shippingAddress->getDistrictId());
                        }
                        if ($shippingAddress->getWardId() !== null) {
                            $item["ward"] = $this->getWard($shippingAddress->getWardId());
                        }
                    }
                }
            }
        }

        return $dataSource;
    }

    private function getDistrict($districtId)
    {
        return $this->districtCollectionFactory->create()->addFieldToFilter("district_id", $districtId)->getLastItem()->getData("name");
    }

    private function getWard($wardId)
    {
        return $this->wardCollectionFactory->create()->addFieldToFilter("ward_id", $wardId)->getLastItem()->getData("name");
    }
}
