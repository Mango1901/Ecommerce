<?php

namespace Magenest\CustomSource\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;

class AddShippingAddressData implements ObserverInterface
{
    /**
     * @var Json
     */
    private $json;

    public function __construct(Json $json)
    {
        $this->json = $json;
    }

    public function execute(Observer $observer)
    {
        $source = $observer->getSource();
        $postValue = $observer->getRequest()->getParams();
        if (isset($postValue['general']['is_online'])) {
            $source->setData('is_online', $postValue['general']['is_online']);
        }
        if (isset($postValue['general']['is_salable'])) {
            $source->setData('is_salable', $postValue['general']['is_salable']);
        }

        if (isset($postValue['general']['area_code'])) {
            $source->setData('area_code', $postValue['general']['area_code']);
        }

        if (isset($postValue['general']['erp_source_code'])) {
            $source->setData('erp_source_code', $postValue['general']['erp_source_code']);
        }

        if (isset($postValue['general']['shipping_address_rows'])) {
            $source->setData('shipping_address', $this->json->serialize($postValue['general']['shipping_address_rows']));
        } else {
            $source->setData('shipping_address', null);
        }
    }
}
