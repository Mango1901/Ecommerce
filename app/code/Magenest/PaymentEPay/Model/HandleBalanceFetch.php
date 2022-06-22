<?php

namespace Magenest\PaymentEPay\Model;

use Magenest\PaymentEPay\Helper\Data;
use Magenest\PaymentEPay\Logger\Logger;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use phpseclib\Crypt\RSA;

class HandleBalanceFetch
{
    const FETCH_ACCOUNT = '9004';

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
    public function execute()
    {
        $partnerCode = $this->helperData->getPartnerCode();
        $currentDate = $this->date->date();
        $requestId = 'RID_' . $currentDate->format('YmdHis') . '_' . rand(0, 99999);
        $timeRequest = $currentDate->format('Y-m-d H:i:s');

        $rsa = new RSA();
        $rsa->loadKey($this->helperData->getPrivateKey());
        $plaintextArray = [$requestId, $timeRequest, $partnerCode, self::FETCH_ACCOUNT];
        $plaintext = implode("|", $plaintextArray);
        $rsa->setSignatureMode(RSA::SIGNATURE_PKCS1);
        $signature = $rsa->sign($plaintext);
        $signature = base64_encode($signature);

        $dataSend = [
            'RequestId' => $requestId,
            'RequestTime' => $timeRequest,
            'PartnerCode' => $partnerCode,
            'Operation' => self::FETCH_ACCOUNT,
            'Signature' => $signature,
        ];

        try {
            $response = $this->helperData->handleRequest($dataSend);
            if (count($response)) {
                $result = $response['result'];
                $curlErrno = $response['curlErrno'];
                $curlError = $response['curlError'];
                if ($result === false || $curlErrno > 0 || $curlError) {
                    $this->logger->debug('ResponseCode: ' . 11 . ' ResponseMessage: Faill!!  Code: ' . $curlErrno . ' - Msg: ' . $curlError);
                } else {
                    $result = json_decode($result, true);
                    /* Verify Signature in response data*/
                    $this->logger->debug('Request: ' . json_encode($dataSend));
                    $this->logger->debug('TRANSACTION RESULT');
                    $this->logger->debug('Result: ' . json_encode($response));

                    return $result;
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return [];
    }
}
