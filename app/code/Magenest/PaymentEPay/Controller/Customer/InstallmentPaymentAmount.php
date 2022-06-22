<?php
namespace Magenest\PaymentEPay\Controller\Customer;

use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magento\Framework\Controller\ResultFactory;

class InstallmentPaymentAmount extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\Cache\StateInterface
     */
    protected $_cacheState;

    /**
     * @var \Magento\Framework\App\Cache\Frontend\Pool
     */
    protected $_cacheFrontendPool;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    protected $handlePaymentInterface;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context          $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface    $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool     $cacheFrontendPool,
        \Magento\Checkout\Model\Session                $checkoutSession,
        \Magento\Framework\View\Result\PageFactory     $resultPageFactory,
        HandlePaymentInterface                         $handlePaymentInterface,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        parent::__construct($context);
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheState = $cacheState;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->resultPageFactory = $resultPageFactory;
        $this->checkoutSession = $checkoutSession;
        $this->handlePaymentInterface = $handlePaymentInterface;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        //lấy dữ liệu từ ajax gửi sang
        $response = $this->getRequest()->getParams();
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $this->checkoutSession->setInstallmentPaymentAmount($response['amount']);
        $response = $this->handlePaymentInterface->handleInstallmentPaymentListing($response['amount']);
        return $resultJson->setData($response);
    }
}
