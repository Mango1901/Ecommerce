<?php

namespace Magenest\Customer\Block\Address;

use Magenest\Customer\Helper\ConfigHelper;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory as AddressCollectionFactory;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Grid Preference Grid Address Core
 */
class Grid extends \Magento\Customer\Block\Address\Grid
{
    /**
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @param Context $context
     * @param CurrentCustomer $currentCustomer
     * @param AddressCollectionFactory $addressCollectionFactory
     * @param CountryFactory $countryFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param ConfigHelper $configHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CurrentCustomer $currentCustomer,
        AddressCollectionFactory $addressCollectionFactory,
        CountryFactory $countryFactory,
        AddressRepositoryInterface $addressRepository,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        $this->addressRepository = $addressRepository;
        $this->configHelper = $configHelper;
        parent::__construct($context, $currentCustomer, $addressCollectionFactory, $countryFactory, $data);
    }

    /**
     * Get customer address by ID
     *
     * @param int $addressId
     * @return AddressInterface|null
     * @throws LocalizedException
     */
    private function getAddressById($addressId)
    {
        try {
            return $this->addressRepository->getById($addressId);
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Get customer's default shipping address
     *
     * @return AddressInterface
     * @throws LocalizedException
     */
    public function getDefaultShippingAddress()
    {
        try {
            $customer = $this->getCustomer();
            if ($customer) {
                return $this->getAddressById($customer->getDefaultShipping());
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * Get customer's default billing address
     *
     * @return AddressInterface
     */
    public function getDefaultBillingAddress()
    {
        try {
            $customer = $this->getCustomer();
            if ($customer) {
                return $this->getAddressById($customer->getDefaultBilling());
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * @param AddressInterface $address
     * @return string
     */
    public function getNameCustomerAddress($address)
    {
        $isEnabledFullName = $this->configHelper->isEnabledFullNameInstead();
        if ($isEnabledFullName) {
            $fullNameAttribute = $address->getCustomAttribute('fullname');
            if ($fullNameAttribute && $fullNameAttribute->getValue()) {
                return $fullNameAttribute->getValue();
            }
        }
        return $address->getLastname() . ' ' . $address->getFirstname();
    }

    /**
     * @param AddressInterface $address
     * @return string
     */
    public function getCustomerAddress($address)
    {
        return implode(', ', [
            $this->getStreetAddress($address),
            $address->getCity(),
            $this->getCountryByCode($address->getCountryId())
        ]);
    }
}
