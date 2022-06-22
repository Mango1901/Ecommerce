<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\PaymentEPay\Model;

use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
use Magenest\PaymentEPay\Helper\Data;
use Magenest\PaymentEPay\Helper\TripleDES;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderFactory;
use Magento\Vault\Api\PaymentTokenManagementInterface;

/**
 * HandlePayment
 */
class HandlePayment implements HandlePaymentInterface
{

    /**
     * PaymentTokenManagementInterface $paymentTokenManagement
     */
    private $paymentTokenManagement;

    private $serializer;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var TripleDES
     */
    protected $helperTripleDES;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curl;
    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_checkoutSession;

    /**
     * HandlePayment constructor.
     * @param PaymentTokenManagementInterface $paymentTokenManagement
     * @param SerializerInterface $serializer
     * @param Data $helperData
     * @param TripleDES $helperTripleDES
     * @param OrderFactory $orderFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\HTTP\Client\Curl $curl
     * @param RemoteAddress $remoteAddress
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\UrlInterface $urlInterface
     */
    public function __construct(
        PaymentTokenManagementInterface $paymentTokenManagement,
        SerializerInterface $serializer,
        Data $helperData,
        TripleDES $helperTripleDES,
        OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\HTTP\Client\Curl $curl,
        RemoteAddress $remoteAddress,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\UrlInterface $urlInterface
    ) {
        $this->_checkoutSession = $checkoutSession;
        $this->_storeManager = $storeManager;
        $this->remoteAddress = $remoteAddress;
        $this->curl = $curl;
        $this->paymentTokenManagement = $paymentTokenManagement;
        $this->serializer = $serializer;
        $this->helperData = $helperData;
        $this->orderFactory    = $orderFactory;
        $this->helperTripleDES = $helperTripleDES;
        $this->customerSession = $customerSession;
        $this->_urlInterface = $urlInterface;
    }

    public function getGatewayToken($customerId, $payType)
    {
        $payTokenKey = '';
        $paymentTokenList = $this->paymentTokenManagement->getListByCustomerId($customerId);
        if (!empty($paymentTokenList)) {
            foreach ($paymentTokenList as $paymentToken) {
                if ($paymentToken->getData('payment_method_code') == PaymentAttributeInterface::CODE_VNPT_EPAY) {
                    $payTypeToken = $this->serializer->unserialize($paymentToken->getData('details'))['maskedCC'];
                    if ($payType === $payTypeToken) {
                        $payTokenKey = $paymentToken->getData('gateway_token');
                    }
                }
            }
            return $payTokenKey;
        }
    }

    public function getDataPayment($orderIncrementId): array
    {
        $data = [];
        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
        $order->setState(Order::STATE_NEW);
        $order->setStatus('pending_payment');
        $order->save();
        $data['timeStamp'] = date('YmdHis');
        $data['merTrxId'] =  $this->getMerId() . $data['timeStamp'] . '_' . rand(100, 10000);
        $data['invoiceNo'] =  $orderIncrementId;
        $data['goodsNm'] =  $order->getId();
        $data['amount'] = floatval($order->getBaseGrandTotal());
        $data['description'] = 'TT Hoa Don: ' . $data['invoiceNo'];
        $data['order'] = $order;
        if ($order->getBaseCurrencyCode() !== 'VND') {
            $data['notification'] = 'Invalid Currency. Please contact administrator for more information.';
        }
        return $data;
    }

    public function getDataQRPayment($orderIncrementId): array
    {
        $data = [];
        $order = $this->orderFactory->create()->loadByIncrementId($orderIncrementId);
        $order->setState(Order::STATE_NEW);
        $order->setStatus('pending_payment');
        $order->save();
        $data['timeStamp'] = date('YmdHis');
        $data['merTrxId'] =  $this->getQRMerId() . $data['timeStamp'] . '_' . rand(100, 10000);
        if (!empty($this->_checkoutSession->getInstallmentPaymentAmount())) {
            $data['invoiceNo'] = $orderIncrementId . '-1';
        } else {
            $data['invoiceNo'] = $orderIncrementId;
        }
        $data['goodsNm'] =  $order->getId();
        $data['amount'] = floatval($order->getBaseGrandTotal());
        $data['description'] = 'TT Hoa Don: ' . $data['invoiceNo'];
        $data['order'] = $order;
        if ($order->getBaseCurrencyCode() !== 'VND') {
            $data['notification'] = 'Invalid Currency. Please contact administrator for more information.';
        }
        return $data;
    }

    public function getDataInstallmentPayment($amount): array
    {
        $data = [];
        $data['timeStamp'] = date('YmdHis');
        $data['merTrxId'] = $this->getQRMerId() . $data['timeStamp'] . '_' . rand(100, 10000);
        $data['amount'] = $amount;

        return $data;
    }

    /**
     * @throws LocalizedException
     */
    public function handlePaymentNotToken($orderIncrementId, $payOption): array
    {
        $data = $this->getDataPayment($orderIncrementId);
        $notification = '';
        if (isset($data['notification']) && !empty($data['notification'])) {
            $notification = $data['notification'];
        }
        if ($payOption == 'PAY_AND_CREATE_TOKEN' || $payOption == 'PAY_WITH_RETURNED_TOKEN' || $payOption == '') {
            $plainTxtToken = $data['timeStamp'] . $data['merTrxId'] . $this->getMerId() . $data['amount'] . $this->getEncodeKey();
            $token = hash('sha256', $plainTxtToken);
            $result = [
                'success' => true,
                'description' => $data['description'],
                'amount' => $data['amount'],
                'token' => $token,
                'timeStamp' => $data['timeStamp'],
                'merId' => $this->getMerId(),
                'invoiceNo' => $data['invoiceNo'],
                'goodsNm' => $data['goodsNm'],
                'merTrxId' => $data['merTrxId'],
                'domain' => $this->getDomain(),
                'customerFirstName' => $data['order']->getCustomerFirstname(),
                'lastname' => $data['order']->getCustomerLastname(),
                'email' => $data['order']->getCustomerEmail(),
                'userId' => $data['order']->getCustomerId(),
                'reqDomain' => $this->_urlInterface->getBaseUrl(),
                'notification' => $notification,
                "userIP"=> $this->getUserIp(),
                "vaStartDt"=> date("YmdHis"),
                "vaEndDt"=> date("Ymd235959"),
                "vaCondition"=>'03'
            ];
        }
        return $result;
    }

    public function handlePaymentWithToken($orderIncrementId, $payOption, $payType): array
    {
        $data = $this->getDataPayment($orderIncrementId);
        $notification = '';
        if (isset($data['notification']) && !empty($data['notification'])) {
            $notification = $data['notification'];
        }
        if ($payOption == 'PAY_WITH_TOKEN' || $payOption == 'PURCHASE_OTP') {
            $payToken = $this->getGatewayToken($data['order']->getCustomerId(), $payType);
            $clearPayToken = $this->helperTripleDES->decrypt3DES($payToken, $this->getDecript3DS());
            $encryptedPayToken = $this->helperTripleDES->encrypt3DES($clearPayToken, $this->getEncript3DS());
            $plainTxtToken = $data['timeStamp'] . $data['merTrxId'] . $this->getMerId() . $data['amount'] . $encryptedPayToken . $this->getEncodeKey();
            $token = hash('sha256', $plainTxtToken);
            $result = [
                'success' => true,
                'description' => $data['description'],
                'amount' => $data['amount'],
                'token'  => $token,
                'timeStamp' => $data['timeStamp'],
                'merId' => $this->getMerId(),
                'invoiceNo' => $data['invoiceNo'],
                'merTrxId' => $data['merTrxId'],
                'domain' => $this->getDomain(),
                'payToken' => $encryptedPayToken,
                'email' => $data['order']->getCustomerEmail(),
                'userId' => $data['order']->getCustomerId(),
                'reqDomain' => $this->_urlInterface->getBaseUrl(),
                'notification' => $notification
            ];
        }
        return $result;
    }

    public function handleCancelTrans($trxId, $refundAmount, $payType, $cancelMsg)
    {
        $cancelPassword = $this->helperData->isTest() ? $this->helperData->getTestCancelPassword() : $this->helperData->getCancelPassword();
        $encode_key = $this->helperData->isTest() ? $this->helperData->getTestEncodeKey() : $this->helperData->getEncodeKey();
        $canceltrans_url = $this->helperData->isTest() ? $this->helperData->getTestCancelTransUrl() : $this->helperData->getCancelTransUrl();
        $timeStamp = date('YmdHis');
        $merId = $this->helperData->isTest() ? $this->helperData->getTestMerId() : $this->helperData->getMerId();
        $merTrxId = 'REFUND_ID' . $timeStamp . '_' . rand(100, 10000);
        $dataToken = $timeStamp . $merTrxId . $trxId . $merId . $refundAmount . $encode_key;
        $merchantToken = hash('sha256', $dataToken);
        $reqStr = 'trxId=' . $trxId . '&merId=' . $merId . '&merTrxId=' . $merTrxId . '&amount=' . $refundAmount
            . '&payType=' . $payType . '&cancelMsg=' . $cancelMsg . '&timeStamp=' . $timeStamp
            . '&merchantToken=' . $merchantToken . '&cancelPw=' . urlencode($cancelPassword);

        return $this->sendRequest($reqStr, $canceltrans_url);
    }

    public function handleCancelTransIS($trxId, $payType, $type, $cancelMsg)
    {
        $cancelPassword = $this->helperData->ISPaymentIsTest() ? $this->helperData->getISTestCancelPassword() : $this->helperData->getISCancelPassword();
        $encode_key = $this->helperData->ISPaymentIsTest() ? $this->helperData->getISPaymentTestEncodeKey() : $this->helperData->getISPaymentEncodeKey();
        $canceltrans_url = $this->helperData->ISPaymentIsTest() ? $this->helperData->getISTestCancelTransUrl() : $this->helperData->getISCancelTransUrl();
        $timeStamp = date('YmdHis');
        $merId = $this->helperData->ISPaymentIsTest() ? $this->helperData->getISPaymentTestMerId() : $this->helperData->getISPaymentMerId();
        $merTrxId = 'REFUND_ID' . $timeStamp . '_' . rand(100, 10000);
        $dataToken = $timeStamp . $merTrxId . $trxId . $merId . $type . $encode_key;
        $merchantToken = hash('sha256', $dataToken);
        $reqStr = [
            "trxId" => $trxId,
            "merId" => $merId,
            "merTrxId" => $merTrxId,
            "type" => $type,
            "cancelMsg" => "123",
            "timeStamp" => $timeStamp,
            "merchantToken" => $merchantToken,
            "cancelPw" => urlencode($cancelPassword)
        ];

        return $this->sendRequestArray($reqStr, $canceltrans_url);
    }

    public function getDomain()
    {
        $domain = '';
        if ($this->helperData->isTest()) {
            $domain = $this->helperData->getTestDomain();
        } else {
            $domain = $this->helperData->getDomain();
        }
        return $domain;
    }

    public function getISDomain()
    {
        $domain = '';
        if ($this->helperData->ISPaymentIsTest()) {
            $domain = $this->helperData->getISPaymentTestDomain();
        } else {
            $domain = $this->helperData->getISPaymentDomain();
        }
        return $domain;
    }

    public function getISDomainListing()
    {
        $domain = '';
        if ($this->helperData->ISPaymentIsTest()) {
            $domain = $this->helperData->getISPaymentTestDomainListing();
        } else {
            $domain = $this->helperData->getISPaymentDomainListing();
        }
        return $domain;
    }

    public function getMerId()
    {
        $merId = '';
        if ($this->helperData->isTest()) {
            $merId = $this->helperData->getTestMerId();
        } else {
            $merId = $this->helperData->getMerId();
        }
        return $merId;
    }

    public function getEncodeKey()
    {
        $encodeKey = '';
        if ($this->helperData->isTest()) {
            $encodeKey = $this->helperData->getTestEncodeKey();
        } else {
            $encodeKey = $this->helperData->getEncodeKey();
        }
        return $encodeKey;
    }

    public function getEncript3DS()
    {
        $encript3DS = '';
        if ($this->helperData->isTest()) {
            $encript3DS = $this->helperData->getTestKey3DEn();
        } else {
            $encript3DS = $this->helperData->getKey3DEn();
        }
        return $encript3DS;
    }

    public function getDecript3DS()
    {
        $decript3DS = '';
        if ($this->helperData->isTest()) {
            $decript3DS = $this->helperData->getTestKey3DDe();
        } else {
            $decript3DS = $this->helperData->getKey3DDe();
        }
        return $decript3DS;
    }

    public function checkTransStatus($merTrxId)
    {
        $timeStamp = date('YmdHis');
        $merId = $this->helperData->isTest() ? $this->helperData->getTestMerId() : $this->helperData->getMerId();
        $encodeKey = $this->helperData->isTest() ? $this->helperData->getTestEncodeKey() : $this->helperData->getEncodeKey();
        $checkTransUrl = $this->helperData->isTest() ? $this->helperData->getTestCheckTransUrl() : $this->helperData->getCheckTransUrl();
        $dataToken = $timeStamp . $merTrxId . $merId . $encodeKey;
        $merchantToken = hash('sha256', $dataToken);
        $reqStr = 'merId=' . $merId . '&merTrxId=' . $merTrxId . '&merchantToken=' . $merchantToken
            . '&timeStamp=' . $timeStamp;
        $transResult = $this->sendRequest($reqStr, $checkTransUrl);
        $resultToJson = json_decode($transResult, true);
        $data = $resultToJson['data'];
        $checkToken = $this->helperData->checkToken($data);
        if ($checkToken) {
            return $transResult;
        } else {
            return __('Invalid Merchant Token.');
        }
    }

    private function sendRequest($dataRequest, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataRequest);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        try {
            $result = curl_exec($ch);
            $curlErrno = curl_errno($ch);
            $curlError = curl_error($ch);
            if ($result === false || $curlErrno > 0 || $curlError) {
                return $curlErrno;
            } else {
                return $result;
            }
            curl_close($ch);
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * @param string $orderIncrementId
     * @return array
     */
    public function handlePaymentWithNoOption($orderIncrementId)
    {
        $data = $this->getDataPayment($orderIncrementId);
        $plainTxtToken = $data['timeStamp'] . $data['merTrxId'] . $this->getMerId() . $data['amount'] . $this->getEncodeKey();
        $token = hash('sha256', $plainTxtToken);
        return [
            'merId' => $this->getMerId(),
            'currency' => "VND",
            'amount' => $data['amount'],
            'invoiceNo' => $data['invoiceNo'],
            'goodsNm' => $data['goodsNm'],
            'payType' => "NO",
            'buyerFirstNm' => $data['order']->getCustomerFirstname(),
            'buyerLastNm' => $data['order']->getCustomerLastname(),
            'buyerPhone' => $data['order']->getCustomerTelephone(),
            'buyerEmail' => $data['order']->getCustomerEmail(),
            'vat' => 0,
            'fee' => 0,
            'notax' => 0,
            'description' => $data['description'],
            'merchantToken' => $token,
            'userIP' => $data['order']->getRemoteIp(),
            'userLanguage' => "VN",
            'timeStamp' => $data['timeStamp'],
            'userId' => $data['order']->getCustomerId(),
            'merTrxId' => $data['merTrxId'],
            'customerFirstName' => $data['order']->getCustomerFirstname(),
            'lastname' => $data['order']->getCustomerLastname(),
            'email' => $data['order']->getCustomerEmail(),
            'reqDomain' => $this->_urlInterface->getBaseUrl(),
        ];
    }

    public function handleQRCodePayment($orderIncrementId, $payOption): array
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $data = $this->getDataQRPayment($orderIncrementId);
        if (!empty($this->_checkoutSession->getInstallmentPaymentAmount())) {
            $data['amount'] = $data['amount'] - $this->_checkoutSession->getInstallmentPaymentAmount();
            $start_date = date('YmdHis', time());
            $end_date = date('YmdHis', strtotime('+30 minutes'));
        } else {
            $start_date = date('YmdHis', time());
            $end_date = date('YmdHis', strtotime('+5 minutes'));
        }
        $notification = '';
        if (isset($data['notification']) && !empty($data['notification'])) {
            $notification = $data['notification'];
        }
        if ($payOption == 'PAY_AND_CREATE_TOKEN' || $payOption == 'PAY_WITH_RETURNED_TOKEN' || $payOption == '') {
            $plainTxtToken = $data['timeStamp'] . $data['merTrxId'] . $this->getQRMerId() . $data['amount'] . $this->getQRPaymentEncodeKey();
            $token = hash('sha256', $plainTxtToken);
            $result = [
                'success'=>true,
                'merId' => $this->getQRMerId(),
                'currency'=>"VND",
                'amount' => $data['amount'],
                'invoiceNo' => $data['invoiceNo'],
                'goodsNm' => $data['goodsNm'],
                "notiUrl" => $this->_storeManager->getStore()->getBaseUrl() . "epay/payment/qrpaymentipn",
                'description' => $data['description'],
                'merchantToken' => $token,
                "userIP"=> $this->getUserIp(),
                'timeStamp' => $data['timeStamp'],
                'merTrxId' => $data['merTrxId'],
                "vaStartDt"=> $start_date,
                "vaEndDt"=> $end_date,
                'notification' => $notification
            ];
        }
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $this->getQRPaymentDomain(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => http_build_query($result),
            CURLOPT_HTTPHEADER => [
                "cache-control: no-cache",
                "content-type: application/x-www-form-urlencoded"
            ],
        ]);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if (!$err) {
            return json_decode($response, true);
        } else {
            return json_decode($err, true);
        }
    }

    public function handleInstallmentPaymentListing($amount): array
    {
        try {
            $isListingUrl = $this->getISDomainListing();
            date_default_timezone_set('Asia/Ho_Chi_Minh');
            $data = $this->getDataInstallmentPayment($amount);
            $plainTxtToken = $data['timeStamp'] . $data['merTrxId'] . $this->getISPaymentMerId() . $data['amount'] . $this->getISEncodeKey();
            $token = hash('sha256', $plainTxtToken);
            $reqStr = [
                "merId" => $this->getISPaymentMerId(),
                "amount" => $data["amount"],
                'merchantToken' => $token,
                "timeStamp" => $data["timeStamp"],
                'merTrxId' => $data['merTrxId'],
            ];

            $response = json_decode($this->sendRequestArray($reqStr, $isListingUrl), true);
            $encodeKey = substr($this->getISEncodeKey(), 0, 24);
            return json_decode($this->helperTripleDES->decrypt3DES($response["data"], $encodeKey), true);
        } catch (\Exception $e) {
            return [];
        }
    }

    public function handleInstallmentPayment($orderIncrementId, $payOption): array
    {
        $data = $this->getDataPayment($orderIncrementId);
        $installmentPaymentValue = $this->_checkoutSession->getInstallmentPaymentValue();
        $amountIS = $this->_checkoutSession->getInstallmentPaymentAmount();
        if (!isset($amountIS)) {
            $amountIS = $data['amount'];
        }
        $this->_checkoutSession->setInstallmentPaymentValue(null);
        $notification = '';
        if (isset($data['notification']) && !empty($data['notification'])) {
            $notification = $data['notification'];
        }
        if ($payOption == 'PAY_AND_CREATE_TOKEN' || $payOption == 'PAY_WITH_RETURNED_TOKEN' || $payOption == '') {
            $plainTxtToken = $data['timeStamp'] . $data['merTrxId'] . $this->getISPaymentMerId() . $amountIS . $this->getISEncodeKey();
            $token = hash('sha256', $plainTxtToken);
            $result = [
                'success' => true,
                'description' => $data['description'],
                'amount' => $amountIS,
                'token' => $token,
                'timeStamp' => $data['timeStamp'],
                'merId' => $this->getISPaymentMerId(),
                'invoiceNo' => $data['invoiceNo'],
                'goodsNm' => $data['goodsNm'],
                'merTrxId' => $data['merTrxId'],
                'domain' => $this->getISDomain(),
                'customerFirstName' => $data['order']->getCustomerFirstname(),
                'lastname' => $data['order']->getCustomerLastname(),
                'email' => $data['order']->getCustomerEmail(),
                'userId' => $data['order']->getCustomerId(),
                'reqDomain' => $this->_urlInterface->getBaseUrl(),
                'notification' => $notification,
                "userIP" => $this->getUserIp(),
                "vaStartDt" => date("YmdHis"),
                "vaEndDt" => date("Ymd235959"),
                "vaCondition" => '03',
                "bankCode" => isset($installmentPaymentValue["bankCode"]) ? $installmentPaymentValue["bankCode"] : "",
                "termIs" => isset($installmentPaymentValue["termIs"]) ? $installmentPaymentValue["termIs"] : "",
            ];
        }
        return $result;
    }

    public function sendRequestArray($dataRequest, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dataRequest));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 180);
        curl_setopt($ch, CURLOPT_TIMEOUT, 180);
        try {
            $result = curl_exec($ch);
            $curlErrno = curl_errno($ch);
            $curlError = curl_error($ch);
            if ($result === false || $curlErrno > 0 || $curlError) {
                return $curlErrno;
            } else {
                return $result;
            }
            curl_close($ch);
        } catch (\Exception $e) {
            return $e;
        }
    }

    private function getUserIp()
    {
        return $this->remoteAddress->getRemoteAddress();
    }

    private function getQRMerId()
    {
        $merId = '';
        if ($this->helperData->QRPaymentIsTest()) {
            $merId = $this->helperData->getQRPaymentTestMerId();
        } else {
            $merId = $this->helperData->getQRPaymentMerId();
        }
        return $merId;
    }

    private function getQRPaymentEncodeKey()
    {
        $encodeKey = '';
        if ($this->helperData->QRPaymentIsTest()) {
            $encodeKey = $this->helperData->getQRPaymentTestEncodeKey();
        } else {
            $encodeKey = $this->helperData->getQRPaymentEncodeKey();
        }
        return $encodeKey;
    }

    private function getQRPaymentDomain()
    {
        $domain = '';
        if ($this->helperData->QRPaymentIsTest()) {
            $domain = $this->helperData->getQRPaymentTestDomain();
        } else {
            $domain = $this->helperData->getQRPaymentDomain();
        }
        return $domain;
    }

    private function getISPaymentMerId()
    {
        $merId = '';
        if ($this->helperData->ISPaymentIsTest()) {
            $merId = $this->helperData->getISPaymentTestMerId();
        } else {
            $merId = $this->helperData->getISPaymentMerId();
        }
        return $merId;
    }

    private function getISEncodeKey()
    {
        $encodeKey = '';
        if ($this->helperData->ISPaymentIsTest()) {
            $encodeKey = $this->helperData->getISPaymentTestEncodeKey();
        } else {
            $encodeKey = $this->helperData->getISPaymentEncodeKey();
        }
        return $encodeKey;
    }
}
