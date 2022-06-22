<?php

namespace Magenest\CustomCheckout\Controller\Onepage;

use Magenest\CustomCheckout\Helper\ConfigHelper;
use Magenest\CustomCheckout\Model\HashParam;
use Magenest\CustomCheckout\Model\HashParamFactory;
use Magento\Checkout\Controller\Onepage\Success as MagentoSuccess;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Translate\InlineInterface;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magenest\CustomCheckout\Model\ResourceModel\HashParam as HashParamResourceModel;
use Psr\Log\LoggerInterface;

/**
 * Class Success
 *
 * Used for redirect to order detail
 */
class Success extends MagentoSuccess
{
    /**
     *  Param Order Id
     */
    const PARAM_ORDER_GUEST = [
        'oar_order_id',
        'oar_type',
        'oar_billing_lastname',
        'oar_email',
        'oar_zip'
    ];

    /**
     * Key Param Hash
     */
    const KEY_PARAM_HASH = 'instance';

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ConfigHelper
     */
    private $helperData;

    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @var SerializerInterface
     */
    private $serialize;

    /**
     * @var HashParamFactory
     */
    private $hashParam;

    /**
     * @var HashParamResourceModel
     */
    private $hashParamResourceModel;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        Registry $coreRegistry,
        InlineInterface $translateInline,
        Validator $formKeyValidator,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        CartRepositoryInterface $quoteRepository,
        PageFactory $resultPageFactory,
        LayoutFactory $resultLayoutFactory,
        RawFactory $resultRawFactory,
        JsonFactory $resultJsonFactory,
        ConfigHelper $helperData,
        StoreManagerInterface $storeManager,
        HttpContext $httpContext,
        SerializerInterface $serialize,
        HashParamFactory $hashParam,
        HashParamResourceModel $hashParamResourceModel,
        LoggerInterface $logger
    ) {
        $this->helperData = $helperData;
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->serialize = $serialize;
        $this->hashParam = $hashParam;
        $this->hashParamResourceModel = $hashParamResourceModel;
        $this->logger = $logger;
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    function execute()
    {
        $storeId = $this->storeManager->getStore()->getId();
        if ($this->helperData->getCheckoutRedirect($storeId)) {
            $session = $this->getOnepage()->getCheckout();
            if (!$this->_objectManager->get(\Magento\Checkout\Model\Session\SuccessValidator::class)->isValid()) {
                return $this->resultRedirectFactory->create()->setPath('checkout/cart');
            }
            $orderId = $session->getLastOrderId();
            $incrementId = $session->getLastRealOrder()->getIncrementId();
            $lastRealOrder = $session->getLastRealOrder();
            $customerEmail = $lastRealOrder->getCustomerEmail();
            $customerLastName = $lastRealOrder->getCustomerLastname();
            $payment = $lastRealOrder->getPayment();
            $method = $payment->getMethodInstance();
            $methodCode = $method->getCode();
            if ($methodCode == 'vnpt_epay_is') {
                return parent::execute();
            }
            $this->_eventManager->dispatch(
                'checkout_onepage_controller_success_action',
                [
                    'order_ids' => [$session->getLastOrderId()],
                    'order' => $session->getLastRealOrder()
                ]
            );
            if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH)) {
                return $this->resultRedirectFactory->create()->setPath('sales/order/view/order_id/' . $orderId);
            } else {
                try {
                    /**
                     * @var $hashParam HashParam
                     */
                    $hashParam = $this->hashParam->create();
                    $hashData = [
                        self::PARAM_ORDER_GUEST[0] => $incrementId,
                        self::PARAM_ORDER_GUEST[1] => 'email',
                        self::PARAM_ORDER_GUEST[2] => $customerLastName,
                        self::PARAM_ORDER_GUEST[3] => $customerEmail
                    ];
                    $hashValue = $this->serialize->serialize($hashData);
                    $hashKey = hash('sha256', $hashValue);
                    $hashParam->setHashKey($hashKey);
                    $hashParam->setHashValue($hashValue);
                    $this->hashParamResourceModel->save($hashParam);
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage());
                    $hashKey = '';
                }

                return $this->resultRedirectFactory->create()->setPath('sales/guest/view',
                    [
                        self::KEY_PARAM_HASH => $hashKey
                    ]
                );
            }
        } else {
            return parent::execute();
        }
    }
}
