<?php

namespace Magenest\PaymentEPay\Controller\Payment;

use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magenest\PaymentEPay\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
use Magento\Vault\Model\Ui\VaultConfigProvider;

/**
 * Class IPN
 *
 */
class IPN extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    protected $quoteRepository;

    protected $cartManagement;

    /**
     * @var Data
     */
    protected $helperData;

    protected $_logger;


    /**
     * @var HandlePaymentInterface
     */
    private $handlePaymentInterface;

    /**
     * Response constructor.
     * @param Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param CartRepositoryInterface $cartRepository
     * @param Data $helperData
     * @param Json $json
     * @param \Magenest\PaymentEPay\Logger\Logger $logger
     * @param CartManagementInterface $cartManagement
     */
    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        CartRepositoryInterface $cartRepository,
        Data $helperData,
        Json $json,
        \Magenest\PaymentEPay\Logger\Logger $logger,
        CartManagementInterface $cartManagement,
        HandlePaymentInterface $handlePaymentInterface
    ) {
        parent::__construct($context);
        $this->orderFactory    = $orderFactory;
        $this->helperData = $helperData;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $cartRepository;
        $this->cartManagement = $cartManagement;
        $this->json = $json;
        $this->_logger = $logger;
        $this->handlePaymentInterface = $handlePaymentInterface;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
	public function execute()
	{
        $this->_logger->debug('##############################');
        $data = $this->getRequest()->getContent();
        $resultToJson = json_decode($data, true);
        $resultToJson[VaultConfigProvider::IS_ACTIVE_CODE] = true;
        $returnData     = [];
        try{
            if (!empty($data)) {
                if($resultToJson['status'] == PaymentAttributeInterface::STATUS_REFUND){
                    $this->_logger->debug('Refund Payment'. $data);
                    $this->getResponse()->setHttpResponseCode(200);
                }else{
                    $check = $this->helperData->checkToken($resultToJson);
                    $vpcOrderInfo = $resultToJson['invoiceNo'];
                    $this->_logger->debug('### Ipn order: ' . $vpcOrderInfo . '###');
                    $order = $this->orderFactory->create()->loadByIncrementId($vpcOrderInfo);
                    if (empty($order->getId())) {
                        $this->_logger->debug('### Order not exist: ' . $vpcOrderInfo);
                    }
                    $amount = $resultToJson['amount'];
                    if($check){
                        if ($order->getId()
                        && floatval($amount) === floatval($order->getBaseGrandTotal())) {
                            $payment = $order->getPayment();
                            if(!$payment){
                                $returnData['RspCode'] = '00';
                                $returnData['Message'] = 'Confirm Success';
                                $this->_logger->debug("--- IPN: Order is in creating process. ---");
                            }else{
                                if (isset($resultToJson['resultCd'])) {
                                    if ($resultToJson['resultCd'] == "00_000") {
                                        $payment->setAdditionalInformation($resultToJson);
                                        $payment->save();
                                        $order->setStatus('pending')->save();
                                        $this->_logger->info('IPN Response data: ' . $data);
                                    } else {
                                        $checkstatusTran = $this->handlePaymentInterface->checkTransStatus($resultToJson['merTrxId']);
                                        $order->cancel();
                                        $order->save();
                                        $this->_logger->info('-----------Response failed : ' . $checkstatusTran);
                                        $this->_logger->debug("--- IPN:" . $resultToJson['resultMsg']);
                                    }
                                    $returnData['RspCode'] = '00';
                                    $returnData['Message'] = 'Confirm Success';
                                } else {
                                    $this->_logger->debug('### Response empty');
                                }
                            }
                        }else{
                            $this->_logger->debug('### Response invalid: ' . $order->getIncrementId());
                        }
                    }else{
                        $this->_logger->debug("--- IPN: Invalid Merchant Token.. ---");
                        $checkstatusTran = $this->handlePaymentInterface->checkTransStatus($resultToJson['merTrxId']);
                        $this->_logger->info('-----------Response failed : ' . $checkstatusTran);
                        if ($order) {
                            $order->cancel()->save();
                        }
                    }
                }
            }else{
                $this->_logger->debug('### Response empty');
            }
        }catch (\Exception $exception){
			$this->_logger->debug("--- IPN Error Msg: " . $exception->getMessage() . " ---");
        }
        $this->_logger->debug('##############################');
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setHttpResponseCode(200);
        return $resultJson;
	}

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
