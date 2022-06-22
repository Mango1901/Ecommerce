<?php

namespace Magenest\PaymentEPay\Model;

use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\OrderRepository;

class EpayData implements \Magenest\PaymentEPay\Api\EpayDataInterface
{
    private $json;
    /**
     * @var PrepareOrderRequest
     */
    protected $prepareOrderRequest;
    /**
     * @var UrlInterface
     */
    private $url;

    public function __construct(
        Json $json,
        PrepareOrderRequest $prepareOrderRequest,
        UrlInterface $url
    ) {
        $this->url = $url;
        $this->prepareOrderRequest = $prepareOrderRequest;
        $this->json = $json;
    }

    /**
     * @param string $orderId
     * @param string $payType
     * @param string|null $payOption
     * @return string
     */
    public function prepareOrderData(string $orderId, string $payType, string $payOption = null)
    {
        $result = $this->prepareOrderRequest->execute($orderId,$payType);
        $result = array_merge($this->generateDefaultData(),$result);
        $result['windowType'] = 1;
        $result['orderId'] = $orderId;
        $result['payType'] = $payType;
        $result['payOption'] = $payOption;
        return $this->json->serialize($result);
    }

    protected function generateDefaultData()
    {
        return [
            "callBackUrl" => $this->url->getUrl("epay/payment/response"),
            "notiUrl" => $this->url->getUrl("epay/payment/ipn"),
            "reqDomain" => $this->url->getBaseUrl(),
            "windowColor" => "#0B3B39",
            "vat" => 0,
            "fee" => 0,
            "notax" => 0,
            "userLanguage" => "VN"
        ];
    }
}
