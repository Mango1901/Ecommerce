<?php

namespace Magenest\MapList\Model\DistanceProvider\Goong;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryDistanceBasedSourceSelection\Model\Convert\AddressToString;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterface;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterfaceFactory;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\GetLatLngFromAddressInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterface;

/**
 * @inheritdoc
 */
class GetLatLngFromAddress implements GetLatLngFromAddressInterface
{
    /**
     * @var array
     */
    private $latLngCache = [];

    /**
     * @var LatLngInterface
     */
    private $latLngInterfaceFactory;

    /**
     * @var AddressToString
     */
    private $addressToString;

    /**
     * @var GetGeoCodesForAddress
     */
    private $getGeoCodesForAddress;

    /**
     * @param LatLngInterfaceFactory $latLngInterfaceFactory
     * @param AddressToString $addressToString
     * @param GetGeoCodesForAddress|null $getGeoCodesForAddress
     */
    public function __construct(
        LatLngInterfaceFactory $latLngInterfaceFactory,
        AddressToString $addressToString,
        GetGeoCodesForAddress $getGeoCodesForAddress = null
    ) {
        $this->latLngInterfaceFactory = $latLngInterfaceFactory;
        $this->addressToString = $addressToString;
        $this->getGeoCodesForAddress = $getGeoCodesForAddress ?: ObjectManager::getInstance()
            ->get(GetGeoCodesForAddress::class);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(AddressInterface $address): LatLngInterface
    {
        $cacheKey = $this->addressToString->execute($address);

        if (!isset($this->latLngCache[$cacheKey])) {
            $res = $this->getGeoCodesForAddress->execute($address);
            $location = $res['results'][0]['geometry']['location'];
            $this->latLngCache[$cacheKey] = $this->latLngInterfaceFactory->create([
                'lat' => (float)$location['lat'],
                'lng' => (float)$location['lng'],
            ]);
        }

        return $this->latLngCache[$cacheKey];
    }
}
