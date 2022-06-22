<?php

namespace Magenest\CustomCheckout\Helper;

use Magenest\CustomCheckout\Model\HashParam;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magenest\CustomCheckout\Controller\Onepage\Success as SuccessController;
use Magento\Sales\Model\Order;
use Magenest\CustomCheckout\Model\HashParamFactory;
use Magenest\CustomCheckout\Model\ResourceModel\HashParam as HashParamResourceModel;
use Psr\Log\LoggerInterface;
use \Magento\Framework\UrlInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magento\Framework\Stdlib\CookieManagerInterface;

/**
 * Class ConfigHelper
 */
class ConfigHelper extends AbstractHelper
{
    /**
     * @var RedirectFactory $redirectFactory
     */
    private $redirectFactory;

    /**
     * @var HttpContext $httpContext
     */
    private $httpContext;
    /**
     * @var Order $order
     */
    private $order;
    /**
     * @var SerializerInterface $serializer
     */
    private $serializer;
    /**
     * @var HashParamFactory $hashParam
     */
    private $hashParam;
    /**
     * @var HashParamResourceModel $hashParamResourceModel
     */
    private $hashParamResourceModel;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Url Builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var HandlePaymentInterface
     */
    protected $handlePaymentInterface;
    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    protected $cookieMetadataFactory;

    protected $sessionManager;

    /**
     * @param RedirectFactory $redirectFactory
     * @param Context $context
     */
    public function __construct(
        RedirectFactory $redirectFactory,
        HttpContext $httpContext,
        Order $order,
        SerializerInterface $serializer,
        HashParamFactory $hashParamFactory,
        HashParamResourceModel $hashParamResourceModel,
        LoggerInterface $logger,
        UrlInterface $_urlBuilder,
        CustomerSession $customerSession,
        HandlePaymentInterface $handlePaymentInterface,
        CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        Context $context
    ) {
        $this->handlePaymentInterface = $handlePaymentInterface;
        $this->serializer = $serializer;
        $this->hashParamResourceModel = $hashParamResourceModel;
        $this->hashParam = $hashParamFactory;
        $this->order = $order;
        $this->httpContext = $httpContext;
        $this->redirectFactory = $redirectFactory;
        $this->logger = $logger;
        $this->_urlBuilder = $_urlBuilder;
        $this->customerSession = $customerSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager = $sessionManager;
        parent::__construct($context);
    }

    const CHECKOUT_NOTI_PATH = 'custom_checkout/general/checkout_notification';
    const CHECKOUT_ENABLE = 'custom_checkout/general/enabled';
    const CHECKOUT_REDIRECT = 'custom_checkout/general/redirect';

    /**
     * @param $storeCode
     * @return mixed
     */
    public function getCheckoutEnabled($storeCode)
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_ENABLE, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * @param $storeCode
     * @return mixed
     */
    public function getCheckoutNoti($storeCode)
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_NOTI_PATH, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    /**
     * @param $storeCode
     * @return mixed
     */
    public function getCheckoutRedirect($storeCode)
    {
        return $this->scopeConfig->getValue(self::CHECKOUT_REDIRECT, ScopeInterface::SCOPE_STORE, $storeCode);
    }

    public function getSalesOrderDetailsRedirect($orderId)
    {

        if ($this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH) && $this->customerSession->isLoggedIn()) {
            $orderEntityId = $this->order->loadByIncrementId($orderId);
            return $this->_urlBuilder->getUrl('sales/order/view/', ['order_id' => $orderEntityId->getId(), '_secure' => true]);
        } else {
            try {
                /**
                 * @var $hashParam HashParam
                 */
                $order = $this->order->loadByIncrementId($orderId);
                $hashParam = $this->hashParam->create();
                $hashData = [
                    SuccessController::PARAM_ORDER_GUEST[0] => $order->getIncrementId(),
                    SuccessController::PARAM_ORDER_GUEST[1] => 'email',
                    SuccessController::PARAM_ORDER_GUEST[2] => $order->getCustomer()->getName(),
                    SuccessController::PARAM_ORDER_GUEST[3] => $order->getCustomer()->getEmail()
                ];
                $hashValue = $this->serializer->serialize($hashData);
                $hashKey = hash('sha256', $hashValue);
                $hashParam->setHashKey($hashKey);
                $hashParam->setHashValue($hashValue);
                $this->hashParamResourceModel->save($hashParam);
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
                $hashKey = '';
            }
            return $this->_urlBuilder->getUrl('sales/guest/view',
                [
                    SuccessController::KEY_PARAM_HASH => $hashKey
                ]);
        }
    }

    public function getQrPaymentData($orderIncrementId)
    {
        $result = $this->handlePaymentInterface->handleQRCodePayment($orderIncrementId, 'PAY_AND_CREATE_TOKEN');
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(1800)
            ->setPath($this->sessionManager->getCookiePath())
            ->setDomain($this->sessionManager->getCookieDomain());

        $this->cookieManager->setPublicCookie(
            'qrCode',
            $result['qrCode'],
            $metadata
        );
        return $result;
    }

    public function getQrCodeCookie()
    {
        return $this->cookieManager->getCookie('qrCode');
    }
}
