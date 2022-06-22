<?php

namespace Magenest\CustomSource\Plugin;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider;
use Magento\InventoryApi\Api\SourceRepositoryInterface;

class ShippingAddressInfo
{
    /**
     * @var SourceRepositoryInterface
     */
    protected $sourceRepository;

    private $json;

    public function __construct(SourceRepositoryInterface $sourceRepository, Json $json)
    {
        $this->sourceRepository = $sourceRepository;
        $this->json = $json;
    }

    /**
     * @param SourceDataProvider $subject
     * @param $data
     * @return array
     */
    public function afterGetData(
        SourceDataProvider $subject,
        $data
    ): array {
        if (isset($data['items'])) {
            return $data;
        }
        $searchCriteria = $subject->getSearchCriteria();
        $result = $this->sourceRepository->getList($searchCriteria)->getItems();
        foreach ($data as $key => &$source) {
            $originalSource = $result[$key];
            $source['general']['is_online'] = $originalSource->getData('is_online');
            $source['general']['is_salable'] = $originalSource->getData('is_salable');
            $source['general']['area_code'] = $originalSource->getData('area_code');
            $source['general']['erp_source_code'] = $originalSource->getData('erp_source_code');
            if (!empty($originalSource->getData('shipping_address'))) {
                $shippingAddress = $originalSource->getData('shipping_address');
                $source['general']['shipping_address_rows'] = $this->json->unserialize($shippingAddress);
            }
        }
        return $data;
    }
}
