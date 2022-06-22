<?php

namespace Magenest\MobileApi\Model\Plugin\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Customer\Api\Data\CustomerExtensionInterface;
use Magento\Framework\App\ObjectManager;

class AddCustomerTelephone
{
    /** @var ExtensionAttributesFactory|mixed|null */
    private $extensionFactory;

    /**
     * AddCustomerTelephone constructor.
     *
     * @param ExtensionAttributesFactory|null $extensionFactory
     */
    public function __construct(
        ExtensionAttributesFactory $extensionFactory = null
    ) {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Set customer telephone
     *
     * @param CustomerRepository $subject
     * @param CustomerInterface $customer
     *
     * @return CustomerInterface
     */
    public function afterGetById(CustomerRepository $subject, CustomerInterface $customer)
    {
        $extensionAttributes = $customer->getExtensionAttributes();
        if (!empty($customer->getCustomAttribute('telephone'))) {
            $telephone = $customer->getCustomAttribute('telephone')->getValue();

            if ($extensionAttributes === null) {
                /** @var CustomerExtensionInterface $extensionAttributes */
                $extensionAttributes = $this->extensionFactory->create(CustomerInterface::class);
                $customer->setExtensionAttributes($extensionAttributes);
            }
            if (!empty($telephone)) {
                $extensionAttributes->setTelephone($telephone);
            }
        }

        return $customer;
    }
}
