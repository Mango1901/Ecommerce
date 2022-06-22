<?php

namespace Magenest\RealShippingMethod\ViewModel;

use Magenest\API247\Model\API247Post;
use Magenest\API247\Model\Carrier\API247;
use Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem;
use Magenest\SelfDelivery\Model\Carrier\SelfDelivery;
use Magenest\ViettelPostCarrier\Model\Carrier\ViettelPost;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Config;
use Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku;

class Carriers implements ArgumentInterface
{
    /**
     * @var bool
     */
    private $existsVU157InSource;

    /** @var UrlInterface */
    private $urlBuilder;

    /** @var Config */
    private $shippingConfig;

    /** @var SourceRepositoryInterface */
    private $sourceRepository;

    /** @var RequestInterface */
    private $request;

    /** @var GetSourceItemsDataBySku */
    private $getSourceItemsDataBySku;

    /**
     * @param Config $shippingConfig
     * @param UrlInterface $urlBuilder
     * @param RequestInterface $request
     * @param SourceRepositoryInterface $sourceRepository
     * @param GetSourceItemsDataBySku $getSourceItemsDataBySku
     */
    public function __construct(
        Config $shippingConfig,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        SourceRepositoryInterface $sourceRepository,
        GetSourceItemsDataBySku $getSourceItemsDataBySku
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
        $this->shippingConfig = $shippingConfig;
        $this->sourceRepository = $sourceRepository;
        $this->getSourceItemsDataBySku = $getSourceItemsDataBySku;
    }

    /**
     * Retrieve carriers
     *
     * @return array
     */
    public function getCarriersForStore($storeId)
    {
        $carriers = [];
        $carriers[''] = __('--Please select--');
        $carrierInstances = $this->shippingConfig->getAllCarriers($storeId);
        foreach ($carrierInstances as $code => $carrier) {
            if ($code == GiaoHangTietKiem::CODE || $code == ViettelPost::CODE || $code === API247::CODE) {
                $carriers[$code] = $carrier->getConfigData('title');
            }
        }
        $carriers['custom'] = __('Other');
        return $carriers;
    }

    /**
     * @param array $items
     * @return array
     */
    public function getAllSourcesAvailable($items)
    {
        $result = [];
        $sourceAllow = [];
        $result[''] = __('--Please select--');
        foreach ($items as $item) {
            $sourceItemsBySku = $this->getSourceItemsDataBySku->execute($item->getSku());
            foreach ($sourceItemsBySku as $sourceItem) {
                if (
                    $sourceItem[SourceItemInterface::QUANTITY] &&
                    $sourceItem[SourceItemInterface::STATUS]
                ) {
                    array_push($sourceAllow, $sourceItem[SourceItemInterface::SOURCE_CODE]);
                }
            }
        }
        $sources =  $this->sourceRepository->getList()->getItems();
        foreach ($sources as $code => $source) {
            $name = $source->getName();
            $city = $source->getCity();
            $district = $source->getDistrict();
            $ward = $source->getWard();
            if (
                isset($city) &&
                isset($district) &&
                isset($ward) &&
                in_array($code, $sourceAllow)
            ) {
                $result[$code] = "$name: $ward, $district, $city";
                if ($source->getErpSourceCode() === 'VU157') {
                    $this->existsVU157InSource[$code] = true;
                }
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getShippingPriceUrl()
    {
        return $this->urlBuilder->getUrl("shipment/shipment/getRealRates");
    }

    /**
     * @return string
     */
    public function getSourceCode()
    {
        return $this->request->getParam('sourceCode');
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isSelfDelivery(Order $order)
    {
        return $order->getShippingMethod() === SelfDelivery::CODE . "_" . SelfDelivery::CODE;
    }

    /**
     * @param $sourceCode
     * @return bool
     */
    public function isExistVU157($sourceCode)
    {
        if ($sourceCode && isset($this->existsVU157InSource[$sourceCode])) {
            return true;
        }

        return false;
    }
}
