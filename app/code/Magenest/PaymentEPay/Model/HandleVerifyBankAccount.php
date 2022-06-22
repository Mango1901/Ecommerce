<?php

namespace Magenest\PaymentEPay\Model;

use Magenest\PaymentEPay\Api\Data\HandleVerifyBankAccountInterface;
use Magenest\PaymentEPay\Logger\Logger;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \phpseclib\Crypt\RSA;
use Magenest\PaymentEPay\Helper\Data;

class HandleVerifyBankAccount implements HandleVerifyBankAccountInterface
{
    const O_VERIFY_ACCOUNT = '9007';

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var TimezoneInterface
     */
    private $date;

    public function __construct(
        Logger $logger,
        Data $helperData,
        TimezoneInterface $date
    ) {
        $this->logger = $logger;
        $this->helperData = $helperData;
        $this->date = $date;
    }

    /**
     * @inheirtDoc
     */
    public function execute($bankNo, $accNo, $accType, $accountName)
    {
        $partnerCode = $this->helperData->getPartnerCode();
        $currentDate = $this->date->date();
        $requestId = 'RID_' . $currentDate->format('YmdHis') .'_'.rand(0,99999);
        $timeRequest = $currentDate->format('Y-m-d H:i:s');

        $rsa = new RSA();
        $rsa->loadKey($this->helperData->getPrivateKey());
        $plaintextArray = [$requestId, $timeRequest, $partnerCode, self::O_VERIFY_ACCOUNT, $bankNo, $accNo, $accType, $accountName];
        $plaintext = implode("|", $plaintextArray);
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $signature = $rsa->sign($plaintext);
        $signature = base64_encode($signature);

        $dataSend = array(
            'RequestId' => $requestId,
            'RequestTime' => $timeRequest,
            'PartnerCode' => $partnerCode,
            'Operation' => self::O_VERIFY_ACCOUNT,
            'BankNo' => $bankNo,
            'AccNo' => $accNo,
            'AccType' => $accType,
            'AccountName' => $accountName,
            'Signature' => $signature,
        );

        try {
            $response = $this->helperData->handleRequest($dataSend);
            if (count($response)) {
                $result = $response['result'];
                $curlErrno = $response['curlErrno'];
                $curlError = $response['curlError'];
                if ($result === false || $curlErrno > 0 || $curlError) {
                    $this->logger->debug('ResponseCode: ' . 11 . ' ResponseMessage: Faill!!  Code: '. $curlErrno.' - Msg: '.$curlError);
                } else {
                    $result = json_decode($result, true);
                    /* Verify Signature in response data*/
                    $plainStr = $result['ResponseCode'].'|'.$result['ResponseMessage'].'|'.$result['RequestId'].'|'.$result['BankNo'].'|'.$result['AccNo'].'|'.$result['AccType'].'|'.$result['ResponseInfo'];
                    $rsa->loadKey($this->helperData->getPublicKey());
                    $this->logger->debug('Request: '. json_encode($dataSend));
                    $this->logger->debug('TRANSACTION RESULT');
                    $this->logger->debug('Result: '. json_encode($response));
                    $this->logger->debug($plainStr);
                    $this->logger->debug('Verify Response Signature: ' . $rsa->verify($plainStr, base64_decode($result['Signature'])));

                    return $result;
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return [];
    }
}
