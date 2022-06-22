<?php
namespace Magenest\FastErp\Model;

use Magenest\CustomAsyncClient\Model\GuzzleCallbackDeferred;
use Magenest\FastErp\Model\ResourceModel\ErpHistoryLog as ErpHistoryLogResourceModel;
use Magento\Backend\App\ConfigInterface;
use Magento\Framework\HTTP\AsyncClient\HttpResponseDeferredInterface;
use Magento\Framework\HTTP\AsyncClient\Request;
use Magento\Framework\HTTP\AsyncClientInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

class AsyncClient extends Client
{
    /** @var GuzzleCallbackDeferred|AsyncClientInterface */
    private $asyncClient;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param AsyncClientInterface $asyncClient
     * @param SerializerInterface $serializer
     * @param ZendClientFactory $httpClientFactory
     * @param ConfigInterface $config
     * @param UrlInterface $url
     * @param ErpHistoryLogResourceModel $erpHistoryLogResourceModel
     * @param ErpHistoryLogFactory $erpHistoryLogFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        AsyncClientInterface       $asyncClient,
        SerializerInterface        $serializer,
        ZendClientFactory          $httpClientFactory,
        ConfigInterface            $config,
        UrlInterface               $url,
        ErpHistoryLogResourceModel $erpHistoryLogResourceModel,
        ErpHistoryLogFactory       $erpHistoryLogFactory,
        LoggerInterface            $logger
    ) {
        $this->serializer  = $serializer;
        $this->asyncClient = $asyncClient;
        parent::__construct(
            $httpClientFactory,
            $config,
            $url,
            $erpHistoryLogResourceModel,
            $erpHistoryLogFactory,
            $logger
        );
    }

    /**
     * @param $params
     * @param $orderId
     *
     * @return HttpResponseDeferredInterface
     */
    public function syncOrder($params, $orderId)
    {
        $request = new Request(
            $this->_getBaseUrl() . $this->_getOrderEndpoint(),
            Request::METHOD_POST,
            [
                'Authorization' => "Bearer " . $this->getAccessToken(),
                'Content-Type'  => 'application/json',
            ],
            $this->serializer->serialize($params)
        );
        return $this->asyncClient->setCallback($this->getErpLogCallback($orderId))->request($request);
    }

    /**
     * @return \Closure
     */
    private function getErpLogCallback($orderId)
    {
        return function ($response) use ($orderId) {
            $historyLog = $this->erpHistoryLogFactory->create();

            $historyLog->setData('order_id', $orderId);
            $historyLog->setData('note', $response->getBody());
            $historyLog->setData('type_erp', 2);
            $historyLog->setData('status', preg_match("/\b2[0-9]{2}\b/", $response->getStatusCode()) === 1 ? 1 : 0);

            $this->erpHistoryLogResourceModel->save($historyLog);
        };
    }
}
