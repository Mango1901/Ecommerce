<?php
namespace Magenest\RePaymentVNPTEpay\Helper;

use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;

class RePaymentHelper extends AbstractHelper
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Session
     */
    protected $session;
    /**
     * @var Json
     */
    protected $json;
    /**
     * @var EncryptorInterface
     */
    protected $_encryptor;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var DriverInterface
     */
    private $driver;

    /** @var RequestInterface */
    private $request;

    protected $orderRepository;

    /**
     * Data constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param RequestInterface $request
     * @param Json $json
     * @param StoreManagerInterface $storeManager
     * @param Session $session
     * @param EncryptorInterface $encryptor
     * @param ComponentRegistrar $componentRegistrar
     * @param DriverInterface $driver
     * @param Context $context
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        RequestInterface $request,
        Json $json,
        StoreManagerInterface $storeManager,
        Session $session,
        EncryptorInterface $encryptor,
        ComponentRegistrar $componentRegistrar,
        DriverInterface $driver,
        Context $context
    ) {
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->json = $json;
        $this->storeManager = $storeManager;
        $this->session = $session;
        $this->_encryptor = $encryptor;
        $this->componentRegistrar = $componentRegistrar;
        $this->driver = $driver;
        parent::__construct($context);
    }

    public function isRepayment(Order $order = null)
    {
        if ($this->request->getParam('instance')) {
            return false;
        }

        if (!isset($order)) {
            $orderId = $this->request->getParam('order_id');
            $order = $this->orderRepository->get($orderId);
        }

        return ($order->getState() === Order::STATE_NEW) ||
            in_array($order->getStatus(), ["pending_payment"]);
    }
}
