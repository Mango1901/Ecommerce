<?php
namespace Magenest\RealShippingMethod\Controller\Adminhtml\Shipment;

use Magenest\API247\Model\API247Post;
use Magenest\API247\Model\Carrier\API247;
use Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem;
use Magenest\ViettelPostCarrier\Model\Carrier\ViettelPost;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class GetRealRates extends Action
{
    /** @var LoggerInterface */
    private $logger;

    /** @var GiaoHangTietKiem */
    private $ghtkCarrier;

    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var SourceRepositoryInterface */
    private $sourceRepository;

    /** @var ViettelPost */
    private $viettelPostCarrier;

    /** @var API247Post */
    private $API247Post;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;
    /**
     * @var API247
     */
    private $API247;

    /**
     * @param Context $context
     * @param ViettelPost $viettelPostCarrier
     * @param GiaoHangTietKiem $ghtkCarrier
     * @param SourceRepositoryInterface $sourceRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context                   $context,
        ViettelPost               $viettelPostCarrier,
        GiaoHangTietKiem          $ghtkCarrier,
        SourceRepositoryInterface $sourceRepository,
        OrderRepositoryInterface  $orderRepository,
        API247Post                $API247Post,
        API247                    $API247,
        ScopeConfigInterface      $scopeConfig,
        LoggerInterface           $logger
    ) {
        $this->logger             = $logger;
        $this->ghtkCarrier        = $ghtkCarrier;
        $this->viettelPostCarrier = $viettelPostCarrier;
        $this->orderRepository    = $orderRepository;
        $this->sourceRepository   = $sourceRepository;
        $this->API247Post         = $API247Post;
        $this->API247             = $API247;
        $this->scopeConfig        = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $params = $this->getRequest()->getParams();

            if (empty($params['method']) || empty($params['pickup_source']) || empty($params['order_id'])) {
                throw new LocalizedException(__('Missing required parameter(s).'));
            }

            $source = $this->sourceRepository->get($params['pickup_source']);
            $order  = $this->orderRepository->get($params['order_id']);

            if ($params['method'] === API247::CODE) {
                $API247 = $this->API247Post->connect();
                $getClientHubId = $API247->getCustomerClientHub();
                $request = new \Magento\Framework\DataObject([
                    'ToProvinceName' => $source->getCity(),
                    'ToDistrictName' => $source->getDistrict(),
                    'ToWardName' => $source->getWard(),
                    'Length' => 0,
                    'Width'  => 0,
                    'Height' => 0,
                    'RealWeight' => $order->getWeight(),
                    'ClientHubID' => $getClientHubId[0]['ClientHubID'],
                    'ServiceTypeID' => $this->scopeConfig->getValue(API247Post::FIXED_SERVICE_TYPES) ?? "DE",
                ]);

                $response = $this->API247->getRates($request);
            } else {
                $request = new \Magento\Framework\DataObject([
                    'source_city'        => $source->getCity(),
                    'source_city_id'     => $source->getCityId(),
                    'source_district'    => $source->getDistrict(),
                    'source_district_id' => $source->getDistrictId(),
                    'source_ward'        => $source->getWard(),
                    'dest_city'          => $order->getShippingAddress()->getCity(),
                    'dest_city_id'       => $order->getShippingAddress()->getCityId(),
                    'dest_district'      => $order->getShippingAddress()->getDistrict(),
                    'dest_district_id'   => $order->getShippingAddress()->getDistrictId(),
                    'weight'             => $order->getWeight(),
                    'package_value'      => $order->getGrandTotal(),
                    'cod'                => $order->getPayment()->getMethod()
                        == Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE
                ]);

                $response = $params['method'] === GiaoHangTietKiem::CODE
                    ? $this->ghtkCarrier->getRates($request)
                    : $this->viettelPostCarrier->getRates($request);
            }


            if ($response['success'] === false && isset($response['message'])) {
                throw new LocalizedException(__($response['message']));
            }

        } catch (\Throwable $e) {
            $this->logger->critical($e);
            $response = ['success' => false, 'message' => $e->getMessage()];
            $resultJson->setHttpResponseCode(Exception::HTTP_INTERNAL_ERROR);
        }

        return $resultJson->setData($response);
    }
}
