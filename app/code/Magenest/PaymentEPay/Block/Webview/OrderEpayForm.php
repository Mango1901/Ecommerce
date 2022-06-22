<?php

namespace Magenest\PaymentEPay\Block\Webview;

use Magenest\PaymentEPay\Helper\Data;
use Magenest\PaymentEPay\Model\EpayData;
use Magenest\PaymentEPay\Model\PrepareOrderRequest;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\MessageInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order;
use Safe\Exceptions\JsonException;

/**
 * Class OrderEpayForm
 */
class OrderEpayForm extends Template
{
    /**
     *
     */
    const ALLOW_EPAY_ATTRIBUTES = [
        'merId',
        'currency',
        'amount',
        'invoiceNo',
        'goodsNm',
        'payType',
        'cardTypeValue',
        'buyerFirstNm',
        'buyerLastNm',
        'buyerPhone',
        'buyerEmail',
        'buyerAddr',
        'buyerCity',
        'buyerState',
        'buyerPostCd',
        'buyerCountry',
        'receiverLastNm',
        'receiverFirstNm',
        'receiverPhone',
        'receiverAddr',
        'receiverCity',
        'receiverState',
        'receiverPostCd',
        'receiverCountry',
        'callBackUrl',
        'notiUrl',
        'reqDomain',
        'vat',
        'fee',
        'notax',
        'description',
        'merchantToken',
        'cardTypeToken',
        'reqServerIP',
        'reqClientVer',
        'userIP',
        'userSessionID',
        'userAgent',
        'userLanguage',
        'timeStamp',
        'domesticToken',
        'payOption',
        'payToken',
        'userId',
        'instmntType',
        'instmntMon',
        'merTrxId',
        'windowColor',
        'windowType',
        'vaStartDt',
        'vaEndDt',
        'vaCondition',
        'mer_temp01',
        'mer_temp02',
        'bankCode',
        'subappid'
    ];
    /**
     * @var Data
     */
    protected $dataHelper;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var PrepareOrderRequest
     */
    protected $prepareOrderRequest;
    /**
     * @var MessageInterface
     */
    protected $message;
    /**
     * @var EpayData
     */
    protected $epayData;
    /**
     * @var OrderFactory
     */
    protected $orderModel;
    /**
     * @var Order
     */
    protected $orderResource;
    /**
     * @var EncryptorInterface
     */
    private $encrypt;

    /**
     * @param OrderFactory $orderModel
     * @param Order $orderResource
     * @param Data $dataHelper
     * @param OrderRepository $orderRepository
     * @param Context $context
     * @param EpayData $epayData
     * @param array $data
     */
    public function __construct(
        OrderFactory $orderModel,
        Order $orderResource,
        Data $dataHelper,
        OrderRepository $orderRepository,
        Context $context,
        EpayData $epayData,
        array $data = []
    ) {
        $this->orderModel = $orderModel;
        $this->orderResource = $orderResource;
        $this->epayData = $epayData;
        $this->orderRepository = $orderRepository;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param $data
     * @return string
     */
    public function prepareFormHtml($data)
    {
        $html = '<form id="megapayForm" name="megapayForm" method="post">';
        $data = $this->getDataToRenderForm($data);
        foreach ($data as $name => $datum) {
            if ($datum === "" || $datum === null) continue;
            $html .= "<input name='$name' type='hidden' value='$datum' />";
        }
        $html .= '</form>';
        return $html;
    }

    /**
     * @param $data
     * @return array
     */
    public function getDataToRenderForm($data)
    {
        $filtered = array_filter($data, function ($value, $key) {
            return in_array($key, self::ALLOW_EPAY_ATTRIBUTES);
        },
                                 ARRAY_FILTER_USE_BOTH
        );
        $filtered['windowType'] = 1;
        return $filtered;
    }

    /**
     * @return mixed
     */
    public function getDomain()
    {
        return $this->dataHelper->getEpayDomain();
    }

    /**
     * @param $postValues
     * @return bool
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function canCurrentOrderPostToEpay($postValues)
    {
        if (isset($postValues['orderId'])) {
            $orderId = $postValues['orderId'];
            $merId = isset($postValues['merId']) ? $postValues['merId'] : null;
            $amount = isset($postValues['amount']) ? $postValues['amount'] : null;
            $invoiceNo = isset($postValues['invoiceNo']) ? $postValues['invoiceNo'] : null;
            if ($merId && $amount && $invoiceNo) {
                $order = $this->orderRepository->get($orderId);
                return $this->dataHelper->canOrderEpay($order);
            }
        }
        return false;
    }

    /**
     * @return array|mixed
     * @throws JsonException
     */
    public function getPayloadRequest()
    {
        $orderId = $this->_request->getParam("orderId");
        $order = $this->orderModel->create();
        $this->orderResource->load($order, $orderId, 'increment_id');
        $data = $this->epayData->prepareOrderData($orderId, "NO");
        $data = \Safe\json_decode($data, 1);
        $data['orderId'] = $order->getId();
        return $data;
    }
}
