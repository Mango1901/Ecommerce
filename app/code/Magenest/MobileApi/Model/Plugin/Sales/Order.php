<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Plugin\Sales;

use Magento\Sales\Api\Data\OrderAddressExtensionInterfaceFactory;

/**
 * Class Order
 * @package Magenest\MobileApi\Model\Plugin\Sales
 */
class Order
{
    /**
     * @var OrderAddressExtensionInterfaceFactory
     */
    protected $_addressExtensionInterfaceFactory;

    /**
     * Constructor.
     * @param OrderAddressExtensionInterfaceFactory $addressExtensionInterfaceFactory
     */
    function __construct(
        OrderAddressExtensionInterfaceFactory $addressExtensionInterfaceFactory
    )
    {
        $this->_addressExtensionInterfaceFactory = $addressExtensionInterfaceFactory;
    }

    /**
     * After get addresses collection
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param \Magento\Sales\Model\ResourceModel\Order\Address\Collection $result
     * @return \Magento\Sales\Model\ResourceModel\Order\Address\Collection
     */
    public function afterGetAddressesCollection(\Magento\Sales\Model\Order $subject, $result)
    {
        if ($subject->getId()) {
            /** @var \Magento\Sales\Api\Data\OrderAddressInterface $address */
            foreach ($result as $address) {
                $extensionAttributes = $address->getExtensionAttributes();
                if(!$extensionAttributes){
                    $extensionAttributes = $this->_addressExtensionInterfaceFactory->create();
                }

                $extensionAttributes->setCityId($address->getCityId());
                $extensionAttributes->setDistrict($address->getDistrict());
                $extensionAttributes->setDistrictId($address->getDistrictId());
                $extensionAttributes->setWard($address->getWard());
                $extensionAttributes->setWardId($address->getWardId());
                $address->setExtensionAttributes($extensionAttributes);
            }
        }

        return $result;
    }
}