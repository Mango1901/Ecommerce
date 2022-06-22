<?php
namespace Magenest\Customer\Observer\Customer;

use Magento\Config\Model\Config\Source\Nooptreq;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Customer;
use Magento\Framework\Api\SearchCriteriaBuilderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magenest\Customer\Service\ObserverCustomerDataProvider;
use Magento\Store\Model\ScopeInterface;

abstract class SaveTelephoneAbstract implements ObserverInterface
{
    const IGNORE_VALIDATE_TELEPHONE = 'ignore_validate_telephone';

    /** @var RequestInterface */
    protected $request;

    /** @var CustomerRepositoryInterface */
    private $customerRepository;

    /** @var SearchCriteriaBuilderFactory */
    private $searchCriteriaBuilder;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * The application registry.
     *
     * @var ObserverCustomerDataProvider
     */
    private $observerCustomerDataProvider;

    /**
     * @param RequestInterface $request
     * @param ScopeConfigInterface $scopeConfig
     * @param CustomerRepositoryInterface $customerRepository
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilder
     * @param ObserverCustomerDataProvider $observerCustomerDataProvider
     */
    public function __construct(
        RequestInterface $request,
        ScopeConfigInterface $scopeConfig,
        CustomerRepositoryInterface $customerRepository,
        SearchCriteriaBuilderFactory $searchCriteriaBuilder,
        ObserverCustomerDataProvider $observerCustomerDataProvider
    ) {
        $this->request = $request;
        $this->scopeConfig = $scopeConfig;
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->observerCustomerDataProvider = $observerCustomerDataProvider;
    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var Customer $customer */
        $customer = $observer->getEvent()->getCustomer();
        $status = $this->scopeConfig->getValue(
            'customer/address/telephone_show',
            ScopeInterface::SCOPE_WEBSITE,
            $customer->getWebsiteId()
        );

        $telephone = $this->getTelephoneFromParams();
        $isIgnore = $this->observerCustomerDataProvider->isIgnoreValidateTelephoneCustomer();

        if (
            !$isIgnore &&
            (
                ($status == Nooptreq::VALUE_REQUIRED && $this->validateTelephone($telephone, $customer->getEntityId()))
                || ($status == Nooptreq::VALUE_OPTIONAL && isset($telephone))
            )
        ) {
            $customer->setData('telephone', $telephone);
        }
    }

    /**
     * @param $phoneNumber
     * @param null $customerId
     * @return bool
     * @throws LocalizedException
     */
    public function validateTelephone($phoneNumber, $customerId = null)
    {
        if (empty($phoneNumber)) {
            throw new LocalizedException(__('Phone Number is required.'));
        }

        $searchCriteriaBuilder = $this->searchCriteriaBuilder->create();
        $searchCriteriaBuilder->addFilter('telephone', $phoneNumber);
        if ($customerId) {
            $searchCriteriaBuilder->addFilter('entity_id', $customerId, 'neq');
        }
        $searchCriteriaBuilder->setPageSize(1)->setCurrentPage(1);

        $customers = $this->customerRepository->getList($searchCriteriaBuilder->create())->getItems();
        if (count($customers) > 0) {
            throw new LocalizedException(__('This phone number is already being used.'));
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getTelephoneFromParams()
    {
        return null;
    }
}
