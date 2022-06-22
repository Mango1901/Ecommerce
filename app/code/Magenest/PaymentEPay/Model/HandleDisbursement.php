<?php

namespace Magenest\PaymentEPay\Model;

use Magenest\PaymentEPay\Api\Data\HandleDisbursementInterface;
use Magenest\PaymentEPay\Helper\Data;
use Magenest\PaymentEPay\Logger\Logger;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \phpseclib\Crypt\RSA;
use Magento\Framework\Filesystem\DriverInterface;

class HandleDisbursement implements HandleDisbursementInterface
{
    const O_VERIFY_ACCOUNT     = '9007';
    const O_DISBURSE           = '9002';
    const O_CHECK_TRANS_STATUS = '9008';
    const O_QUERY_BALANCE      = '9004';

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var ComponentRegistrar
     */
    private $componentRegistrar;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * @var TimezoneInterface
     */
    private $date;

    /**
     * @var Logger
     */
    protected $_logger;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param DriverInterface $driver
     * @param ComponentRegistrar $componentRegistrar
     * @param ReadFactory $readFactory
     * @param TimezoneInterface $date
     * @param Logger $logger
     * @param Data $helperData
     */
    public function __construct(
        DriverInterface    $driver,
        ComponentRegistrar $componentRegistrar,
        ReadFactory        $readFactory,
        TimezoneInterface  $date,
        Logger             $logger,
        Data               $helperData
    ) {
        $this->driver             = $driver;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory        = $readFactory;
        $this->date               = $date;
        $this->_logger            = $logger;
        $this->helperData         = $helperData;
    }

    /**
     * @inheirtDoc
     */
    public function execute($withdraw)
    {
        $partnerCode     = $this->helperData->getPartnerCode();
        $disbursementUrl = $this->helperData->getDisbursementUrl();
        $currentDate     = $this->date->date();
        $requestId       = 'RID_' . $currentDate->format('YmdHis') . '_' . rand(0, 99999);
        $timeRequest     = $currentDate->format('Y-m-d H:i:s');
        $referenceId     = $partnerCode . $currentDate->format('YmdHis') . rand(0, 99999);
        $memo            = 'Transfer message'; //max length is 100 characters

        $bankNo      = $withdraw->getBankNo();
        $accNo       = $withdraw->getAccNo();
        $accType     = $withdraw->getAccType();
        $accountName = $withdraw->getAccountName();
        $amount      = (int)$withdraw->getTransferAmount();

        $rsa = new RSA();
        $rsa->loadKey($this->helperData->getPrivateKey());
        $plaintextArray = [
            $requestId,
            $timeRequest,
            $partnerCode,
            self::O_DISBURSE,
            $referenceId,
            $bankNo,
            $accNo,
            $accType,
            $amount,
            $memo
        ];
        $plaintext      = implode("|", $plaintextArray);
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $signature = $rsa->sign($plaintext);
        $signature = base64_encode($signature);

        $dataSend = [
            'RequestId'     => $requestId,
            'RequestTime'   => $timeRequest,
            'PartnerCode'   => $partnerCode,
            'Operation'     => self::O_DISBURSE,
            'BankNo'        => $bankNo,
            'AccNo'         => $accNo,
            'AccountName'   => $accountName,
            'AccType'       => $accType,
            'ReferenceId'   => $referenceId,
            'RequestAmount' => $amount,
            'Memo'          => $memo,
            /*'ContractNumber' => 2155,*/
            'Signature'     => $signature
        ];

        try {
            $this->_logger->debug(json_encode($dataSend));
            $response = $this->helperData->handleRequest($dataSend);
            if (count($response)) {
                $result    = $response['result'];
                $curlErrno = $response['curlErrno'];
                $curlError = $response['curlError'];
                if ($result === false || $curlErrno > 0 || $curlError) {
                    if ($curlErrno > 0) {
                        if ((int)$dataSend['Operation'] == self::O_DISBURSE
                            || (int)$dataSend['Operation'] == self::O_CHECK_TRANS_STATUS) {
                            $this->_logger->debug(
                                'ResponseCode: ' . 99 . ' ResponseMessage: TimeoutCode: ' . $curlErrno . ' - Msg: '
                                . $curlError
                            );
                        } else {
                            $this->_logger->debug('ResponseCode' . 11 . ' ResponseMessage Fail!!');
                        }
                    }
                } else {
                    $result = json_decode($result, true);

                    /* Verify Signature in response data*/
                    $plainStr = $result['ResponseCode'] . '|' . $result['ResponseMessage'] . '|'
                        . $result['ReferenceId'] . '|' . $result['TransactionId'] . '|' . $result['TransactionTime']
                        . '|' . $result['BankNo'] . '|' . $result['AccNo'] . '|' . $result['AccName'] . '|'
                        . $result['AccType'] . '|' . $result['RequestAmount'] . '|' . $result['TransferAmount'];
                    $rsa->loadKey($this->helperData->getPublicKey());
                    $this->_logger->debug('TRANSACTION RESULT');
                    $this->_logger->debug($plainStr);
//                    $this->_logger->debug(
//                        'Verify Response Signature: ' . $rsa->verify($plainStr, base64_decode($result['Signature']))
//                    );
                    return $result;
                }
            }
        } catch (\Exception $e) {
            $this->_logger->error($e->getMessage());
        }
        return [];
    }
}
