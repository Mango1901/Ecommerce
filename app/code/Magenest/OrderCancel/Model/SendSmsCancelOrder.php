<?php

namespace Magenest\OrderCancel\Model;

use Magenest\OrderCancel\Api\SendSmsCancelOrderInterface;
use Magenest\OrderCancel\Helper\Data;
use Magento\Framework\Webapi\Soap\ClientFactory;
use Psr\Log\LoggerInterface;

class SendSmsCancelOrder implements SendSmsCancelOrderInterface
{
    /**
     * @var ClientFactory
     */
    private $soapClientFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var LoggerInterface
     */
    protected $logger;


    /**
     * @param ClientFactory $soapClientFactory
     * @param Data $helperData
     * @param LoggerInterface $logger
     */
    public function __construct(
        ClientFactory $soapClientFactory,
        Data $helperData,
        LoggerInterface $logger
    ) {
        $this->soapClientFactory = $soapClientFactory;
        $this->helperData = $helperData;
        $this->logger = $logger;
    }

    /**
     * @inheirtDoc
     */
    public function sendSingleSms($message, $phone)
    {
        $brandName = $this->helperData->getBranchName();
        $username = $this->helperData->getUsername();
        $password = $this->helperData->getPassword();
        if ($brandName && $username && $password) {
            try {
                $client = $this->soapClientFactory->create(
                    self::WSDL_LAC_HONG
                );
                // Response is
                $response = $client->SendMTSingle([
                    self::BRANCH_NAME => $brandName,
                    self::USERNAME => $username,
                    self::PASSWORD => $password,
                    self::MESSAGE => $message,
                    self::PHONE => $phone
                ]);

                if ($response && $responseCode = $response->SendMTSingleResult) {
                    if (strpos($responseCode, '200') !== false) {
                        $this->logger->info('======================LOG SEND SMS SUCCESS START=========================');
                        $this->logger->info($responseCode . ' - ' . self::RESPONSE_CODE[200]);
                        $this->logger->info('======================LOG SEND SMS SUCCESS END===========================');
                        return true;
                    } else {
                        $this->logger->error('======================LOG SEND SMS FAILED START=========================');
                        $this->logger->error(self::RESPONSE_CODE[$responseCode]);
                        $this->logger->error('======================LOG SEND SMS FAILED END===========================');
                    }
                }
            } catch (\Exception $exception) {
                $this->logger->error('======================LOG SEND SMS EXCEPTION START=========================');
                $this->logger->error($exception->getMessage());
                $this->logger->error('======================LOG SEND SMS EXCEPTION END===========================');
            }
        }
        return false;
    }
}
