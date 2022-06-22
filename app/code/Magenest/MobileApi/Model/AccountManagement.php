<?php
namespace Magenest\MobileApi\Model;

use Magenest\Customer\Helper\Login;
use Magenest\Customer\Model\LoginByTelephone;
use Magenest\MobileApi\Api\AccountManagementInterface;
use Magenest\MobileApi\Api\Data\CustomerInterface as CustomCustomerInterface;
use Magenest\MobileApi\Api\Data\Customer\VatInvoiceInterface;
use Magenest\MobileApi\Api\ResetPasswordInterfaceFactory as ResetPasswordFactory;
use Magenest\MobileApi\Model\Customer\VatInvoiceFactory;
use Magenest\SocialLogin\Controller\Apple\Connect as AppleConnect;
use Magenest\SocialLogin\Controller\Google\Connect as GoogleConnect;
use Magenest\SocialLogin\Controller\Facebook\Connect as FacebookConnect;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\AccountManagement as CustomerManagement;
use Magento\Customer\Model\AddressRegistry;
use Magento\Customer\Model\AuthenticationInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\InvalidEmailOrPasswordException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Intl\DateTimeFactory;
use Magento\Framework\Json\Helper\Data;
use Magento\Framework\Math\Random;
use Magento\Framework\Stdlib\DateTime;
use Magento\Integration\Model\CredentialsValidator;
use Magento\Integration\Model\Oauth\Token\RequestThrottler;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteManagement;
use Magento\Quote\Model\QuoteRepository;
use Magento\Review\Model\ResourceModel\Review\Product\CollectionFactory as ReviewCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface as PsrLogger;

class AccountManagement implements AccountManagementInterface
{
    /**
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var CustomerManagement
     */
    protected $_accountManagement;

    /**
     * @var QuoteManagement
     */
    protected $_quoteManagement;

    /**
     * @var QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * @var CredentialsValidator
     */
    protected $_validatorHelper;

    /**
     * @var RequestThrottler
     */
    protected $_requestThrottler;

    /**
     * @var TokenModelFactory
     */
    protected $_tokenModelFactory;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var QuoteIdMaskFactory
     */
    protected $_quoteIdMaskFactory;

    /**
     * @var CustomerRepository
     */
    protected $_customerRepository;

    /**
     * @var AuthenticationInterface
     */
    protected $_authentication;

    /**
     * @var VatInvoiceFactory
     */
    protected $_vatInvoiceFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ReviewCollectionFactory
     */
    protected $_reviewCollectionFactory;

    /**
     * @var SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var CollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @var Random
     */
    private $mathRandom;

    /**
     * @var PsrLogger
     */
    protected $logger;

    /**
     * @var AddressRegistry
     */
    private $addressRegistry;

    /**
     * @var CustomerRegistry
     */
    private $customerRegistry;

    /**
     * @var DateTimeFactory
     */
    private $dateTimeFactory;

    /** @var Data */
    protected $jsonHelper;

    /** @var CustomerRepositoryInterface */
    private $_customerRepositoryApi;

    /** @var ResetPasswordFactory */
    private $_resetPassword;

    /** @var AppleConnect */
    private $appleConnect;

    /** @var GoogleConnect */
    private $googleConnect;

    /** @var FacebookConnect */
    private $facebookConnect;

    /** @var LoginByTelephone */
    private $loginByTelephone;

    /**
     * Constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     * @param CustomerManagement $accountManagement
     * @param QuoteManagement $quoteManagement
     * @param QuoteRepository $quoteRepository
     * @param CredentialsValidator $credentialsValidator
     * @param TokenModelFactory $tokenFactory
     * @param CustomerSession $customerSession
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CustomerRepository $customerRepository
     * @param AuthenticationInterface $authentication
     * @param VatInvoiceFactory $vatInvoiceFactory
     * @param StoreManagerInterface $storeManager
     * @param ReviewCollectionFactory $reviewCollectionFactory
     * @param SubscriberFactory $subscriberFactory
     * @param CollectionFactory $customerCollectionFactory
     * @param LoginByTelephone $loginByTelephone
     * @param Random $mathRandom
     * @param PsrLogger $logger
     * @param Data $jsonHelper
     * @param AppleConnect $appleConnect
     * @param GoogleConnect $googleConnect
     * @param FacebookConnect $facebookConnect
     * @param CustomerRepositoryInterface $_customerRepositoryApi
     * @param ResetPasswordFactory $resetPassword
     * @param CustomerRegistry $customerRegistry
     * @param AddressRegistry|null $addressRegistry
     * @param DateTimeFactory|null $dateTimeFactory
     */
    public function __construct(
        DataObjectFactory           $dataObjectFactory,
        CustomerManagement          $accountManagement,
        QuoteManagement             $quoteManagement,
        QuoteRepository             $quoteRepository,
        CredentialsValidator        $credentialsValidator,
        TokenModelFactory           $tokenFactory,
        CustomerSession             $customerSession,
        QuoteIdMaskFactory          $quoteIdMaskFactory,
        CustomerRepository          $customerRepository,
        AuthenticationInterface     $authentication,
        VatInvoiceFactory           $vatInvoiceFactory,
        StoreManagerInterface       $storeManager,
        ReviewCollectionFactory     $reviewCollectionFactory,
        SubscriberFactory           $subscriberFactory,
        CollectionFactory           $customerCollectionFactory,
        LoginByTelephone            $loginByTelephone,
        Random                      $mathRandom,
        PsrLogger                   $logger,
        Data                        $jsonHelper,
        AppleConnect                $appleConnect,
        GoogleConnect               $googleConnect,
        FacebookConnect             $facebookConnect,
        CustomerRepositoryInterface $_customerRepositoryApi,
        ResetPasswordFactory        $resetPassword,
        CustomerRegistry            $customerRegistry,
        AddressRegistry             $addressRegistry = null,
        DateTimeFactory             $dateTimeFactory = null
    ) {
        $this->_dataObjectFactory        = $dataObjectFactory;
        $this->_accountManagement        = $accountManagement;
        $this->_quoteManagement          = $quoteManagement;
        $this->_quoteRepository          = $quoteRepository;
        $this->_tokenModelFactory        = $tokenFactory;
        $this->_validatorHelper          = $credentialsValidator;
        $this->_customerSession          = $customerSession;
        $this->_quoteIdMaskFactory       = $quoteIdMaskFactory;
        $this->_customerRepository       = $customerRepository;
        $this->_authentication           = $authentication;
        $this->_vatInvoiceFactory        = $vatInvoiceFactory;
        $this->_storeManager             = $storeManager;
        $this->appleConnect              = $appleConnect;
        $this->googleConnect             = $googleConnect;
        $this->facebookConnect           = $facebookConnect;
        $this->loginByTelephone          = $loginByTelephone;
        $this->_reviewCollectionFactory  = $reviewCollectionFactory;
        $this->_subscriberFactory        = $subscriberFactory;
        $this->customerCollectionFactory = $customerCollectionFactory;
        $this->mathRandom                = $mathRandom;
        $this->logger                    = $logger;
        $objectManager                   = ObjectManager::getInstance();
        $this->addressRegistry           = $addressRegistry ?: $objectManager->get(AddressRegistry::class);
        $this->customerRegistry          = $customerRegistry;
        $this->dateTimeFactory           = $dateTimeFactory ?: $objectManager->get(DateTimeFactory::class);
        $this->jsonHelper                = $jsonHelper;
        $this->_customerRepositoryApi    = $_customerRepositoryApi;
        $this->_resetPassword            = $resetPassword;
    }

    /**
     * @inheritdoc
     */
    public function updatePassword($customerId, $email, $currentPassword, $newPassword)
    {
        $customer = $this->_customerRepository->getById($customerId);
        if ($email != $customer->getEmail()) {
            throw new InvalidEmailOrPasswordException(__('Invalid customer.'));
        }

        return $this->_accountManagement->changePassword($email, $currentPassword, $newPassword);
    }

    /**
     * @inheritdoc
     */
    public function updateEmail($customerId, $newEmail, $currentPassword)
    {
        $this->_authentication->authenticate($customerId, $currentPassword);
        $customer = $this->_customerRepository->getById($customerId)
            ->setEmail($newEmail);

        $this->_customerRepository->save($customer);

        return $customer;
    }

    /**
     * @inheritdoc
     */
    public function updateProfile($customerId, CustomerInterface $customerData)
    {
        $customer = $this->_customerRepository->getById($customerId)
            ->setFirstname($customerData->getFirstname())
            ->setLastname($customerData->getLastname());

        $this->_customerRepository->save($customer);

        return $customer;
    }

    /**
     * @inheritdoc
     */
    public function signUp(CustomCustomerInterface $customer, $password = null, $guestQuoteId = null)
    {
        if (!\Zend_Validate::is(trim($customer->getEmail()), 'EmailAddress')) {
            throw new LocalizedException(__('Email address invalid.'));
        }

        if ($customer->getTelephone()) {
            $customer->setCustomAttribute(CustomCustomerInterface::TELEPHONE, $customer->getTelephone());
        }

        $this->_accountManagement->createAccount($customer, $password);
        $this->_validatorHelper->validate($customer->getEmail(), $password);
        $this->_getRequestThrottler()->throttle($customer->getEmail(), RequestThrottler::USER_TYPE_CUSTOMER);

        return $customer;
    }

    /**
     * @inheritdoc
     * @throws \Exception
     */
    public function signIn($username, $password = null, $guestQuoteId = null)
    {
        try {
            if (preg_match(Login::REGEX_MOBILE_NUMBER, $username)) {
                $email = $this->loginByTelephone->authenticateByTelephone($username);
            }

            $customer = $this->_accountManagement->authenticate($username, $password);
            $this->_customerSession->setCustomerDataAsLoggedIn($customer);
            $this->_customerSession->regenerateId();
            $this->_getRequestThrottler()->resetAuthenticationFailuresCount(
                $customer->getEmail(),
                RequestThrottler::USER_TYPE_CUSTOMER
            );
        } catch (EmailNotConfirmedException $e) {
            $this->_getRequestThrottler()->logAuthenticationFailure(
                $email ?? $username,
                RequestThrottler::USER_TYPE_CUSTOMER
            );
            throw new AuthenticationException(__($e->getMessage()));
        } catch (\Exception $e) {
            $this->_getRequestThrottler()->logAuthenticationFailure(
                $email ?? $username,
                RequestThrottler::USER_TYPE_CUSTOMER
            );
            throw new AuthenticationException(
                __(
                    'The account sign-in was incorrect or your account is disabled temporarily. '
                    . 'Please wait and try again later.'
                )
            );
        }

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $this->getCustomerResult($customer, $guestQuoteId, true)])->getData();
    }

    /**
     * Reset password through OTP
     *
     * @param string $telephone
     *
     * @return \Magenest\MobileApi\Api\ResetPasswordInterface
     * @throws LocalizedException
     */
    public function resetPassword($telephone)
    {
        $result         = $this->initiatePasswordReset($telephone);
        $_resetPassword = $this->_resetPassword->create();
        if ($result) {
            $_resetPassword->setToken($result['newpasswordtoken']);
            $_resetPassword->setEmail($result['customer_email']);
            $_resetPassword->setStatus($result['status']);
        }

        return $_resetPassword;
    }

    /**
     * @inheritdoc
     */
    public function reviews($customerId)
    {
        $reviewCollection = $this->_reviewCollectionFactory->create()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->addCustomerFilter($customerId)
            ->setDateOrder()
            ->addReviewSummary();

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $reviewCollection->toArray()])->getData();
    }

    /**
     * @inheritdoc
     */
    public function facebookSignIn($accessToken, $guestQuoteId = null)
    {
        $this->facebookConnect->getRequest()->setParams(
            ['error' => true, 'code' => true, 'access_token' => $accessToken, 'state' => true]
        );
        $this->facebookConnect->connect(true);
        $customer = $this->_customerRepository->getById($this->_customerSession->getCustomerId());

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $this->getCustomerResult($customer, $guestQuoteId, true)])->getData();
    }

    /**
     * @inheritdoc
     */
    public function googleSignIn($accessToken, $guestQuoteId = null)
    {
        $this->googleConnect->getRequest()->setParams(
            ['error' => true, 'code' => true, 'access_token' => $accessToken, 'state' => true]
        );
        $this->googleConnect->connect(true);
        $customer = $this->_customerRepository->getById($this->_customerSession->getCustomerId());

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $this->getCustomerResult($customer, $guestQuoteId, true)])->getData();
    }

    /**
     * @inheritdoc
     */
    public function appleSignIn($accessToken, $state = '{}', $guestQuoteId = null)
    {
        $this->appleConnect->getRequest()->setParams(
            ['error' => true, 'code' => true, 'access_token' => $accessToken, 'state' => $state]
        );
        $this->appleConnect->connect();
        $customer = $this->_customerRepository->getById($this->_customerSession->getCustomerId());

        return $this->_dataObjectFactory->create()->addData(
            ['result' => $this->getCustomerResult($customer, $guestQuoteId, true)]
        )->getData();
    }

    /**
     * @inheritdoc
     */
    public function vatInvoice($customerId)
    {
        $customer = $this->_customerRepository->getById($customerId);
        if ($customer->getCustomAttribute('default_vat_invoice')
            && $vatInvoice = $customer->getCustomAttribute('default_vat_invoice')->getValue()
        ) {
            $vatInvoice = \Zend_Json::decode($vatInvoice);

            return $this->_vatInvoiceFactory->create()
                ->setCompanyAddress($vatInvoice['company_address'])
                ->setTaxCode($vatInvoice['tax_code'])
                ->setCompanyName($vatInvoice['company_name']);
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function saveVatInvoice($customerId, VatInvoiceInterface $vatInvoice)
    {
        $customer       = $this->_customerRepository->getById($customerId);
        $vatInvoiceData = [
            'company_name'    => $vatInvoice->getCompanyName(),
            'tax_code'        => $vatInvoice->getCompanyName(),
            'company_address' => $vatInvoice->getCompanyAddress()
        ];

        $customer->setCustomAttribute('default_vat_invoice', \Zend_Json::encode($vatInvoiceData));
        $this->_customerRepository->save($customer);

        return $vatInvoice;
    }

    /**
     * @inheritdoc
     */
    public function saveNewsletter($customerId, $isSubscribed)
    {
        $customer = $this->_customerRepository->getById($customerId);
        $customer->setStoreId($this->_storeManager->getStore()->getId());
        $isSubscribedState = $customer->getExtensionAttributes()
            ->getIsSubscribed();

        if ($isSubscribed !== $isSubscribedState) {
            $customer->setData('ignore_validation_flag', true);
            $this->_customerRepository->save($customer);

            if ($isSubscribed) {
                $subscribeModel = $this->_subscriberFactory->create()
                    ->subscribeCustomerById($customerId);
                return $subscribeModel->getStatus();
            } else {
                $this->_subscriberFactory->create()
                    ->unsubscribeCustomerById($customerId);

                return Subscriber::STATUS_UNSUBSCRIBED;
            }
        }

        return true;
    }

    /**
     * Get customer result
     *
     * @param CustomerInterface $customer
     * @param null|string $guestQuoteId
     *
     * @return array
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getCustomerResult($customer, $guestQuoteId = null, $isViaApi = null)
    {
        $result                  = [];
        $result['access_token']  = $this->_tokenModelFactory->create()
            ->createCustomerToken($customer->getId())
            ->getToken();
        $result['customer_info'] = [
            'id'                        => $customer->getId(),
            'group_id'                  => $customer->getGroupId(),
            'created_at'                => $customer->getCreatedAt(),
            'updated_at'                => $customer->getUpdatedAt(),
            'created_in'                => $customer->getCreatedIn(),
            'email'                     => $customer->getEmail(),
            'firstname'                 => $customer->getFirstname(),
            'lastname'                  => $customer->getLastname(),
            'gender'                    => $customer->getGender(),
            'store_id'                  => $customer->getStoreId(),
            'website_id'                => $customer->getWebsiteId(),
            'telephone'                 => null,
            'addresses'                 => array_map(function ($address) {
                return $address->__toArray();
            }, $customer->getAddresses()),
            'disable_auto_group_change' => $customer->getDisableAutoGroupChange()
        ];

        if ($customer->getCustomAttribute('telephone')) {
            $result['customer_info']['telephone'] = $customer->getCustomAttribute('telephone')->getValue();
        }

        // Access token to know is from rest api or from frontend
        if (!$isViaApi) {
            try {
                $quote = $this->_quoteRepository->getActiveForCustomer($customer->getId());
            } catch (\Exception $e) {
                $cartId = $this->_quoteManagement->createEmptyCartForCustomer($customer->getId());
                $quote  = $this->_quoteRepository->getActive($cartId);
            }

            $result['cart_id'] = $quote->getId();
            if ($guestQuoteId !== null) {
                try {
                    $quoteIdMask = $this->_quoteIdMaskFactory->create()->load($guestQuoteId, 'masked_id');
                    $guestQuote  = $this->_quoteRepository->getActive($quoteIdMask->getQuoteId());
                    $quote->merge($guestQuote);

                    $this->_quoteRepository->delete($guestQuote);
                } catch (\Exception $e) {
                }
            }

            $quote->getBillingAddress();
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->setTotalsCollectedFlag(false)->collectTotals();
            $this->_quoteRepository->save($quote);

            $result['cart_item_qty'] = (int)$quote->getItemsQty();
        }

        return $result;
    }

    /**
     * Get request throttler instance
     * @return RequestThrottler
     * @deprecated 100.0.4
     */
    private function _getRequestThrottler()
    {
        if (!$this->_requestThrottler instanceof RequestThrottler) {
            return ObjectManager::getInstance()->get(RequestThrottler::class);
        }

        return $this->_requestThrottler;
    }

    /**
     * Reset password through OTP
     *
     * @param string $telephone
     *
     * @return mixed
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function initiatePasswordReset(string $telephone)
    {
        // load customer by phone
        $customer_id = !empty($this->getCustomerByPhoneNumber($telephone)->getId()) ?
            $this->getCustomerByPhoneNumber($telephone)->getId() : '';
        if (!empty($customer_id)) {
            $customer = $this->_customerRepositoryApi->getById($customer_id);
            // No need to validate customer address while saving customer reset password token
            $this->disableAddressValidation($customer);

            $newPasswordToken = $this->mathRandom->getUniqueHash();
            if (!empty($newPasswordToken)) {
                $customerSecure = $this->customerRegistry->retrieveSecureData($customer->getId());
                $customerSecure->setRpToken($newPasswordToken);
                $customerSecure->setRpTokenCreatedAt(
                    $this->dateTimeFactory->create()->format(DateTime::DATETIME_PHP_FORMAT)
                );
                $customer->setData('ignore_validation_flag', true);
                $this->_customerRepository->save($customer);
            }
        } else {
            return [
                'newpasswordtoken' => 'Invalid customer telephone',
                'customer_email'   => '',
                'status'           => "false"
            ];
        }
    }

    /**
     * Get customer by Phone number
     *
     * @param string $telephone
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function getCustomerByPhoneNumber(string $telephone)
    {
        $customerCollection = $this->customerCollectionFactory->create();
        return $customerCollection->addAttributeToFilter("telephone", $telephone)->getFirstItem();
    }

    /**
     * Disable Customer Address Validation
     *
     * @param CustomerInterface $customer
     *
     * @throws NoSuchEntityException
     */
    private function disableAddressValidation($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            $addressModel = $this->addressRegistry->retrieve($address->getId());
            $addressModel->setShouldIgnoreValidation(true);
        }
    }

    /**
     * Save customer information with password
     *
     * @param CustomerInterface $customer
     * @param null $password
     *
     * @return CustomerInterface
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function saveCustomerInformation(CustomerInterface $customer, $password = null)
    {
        if ($customer->getId()) {
            /** @var \Magento\Customer\Model\Customer $customerModel */
            $customerModel = $this->customerCollectionFactory->create()
                ->addFieldToFilter("entity_id", $customer->getId())
                ->setCurPage(1)
                ->setPageSize(1)
                ->getFirstItem();
            $passwordHash  = $customerModel->hashPassword($password);
            return $this->_customerRepositoryApi->save($customer, $passwordHash);
        }
        return $customer;
    }
}
