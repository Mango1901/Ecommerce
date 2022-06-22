<?php

namespace Magenest\MapList\Model\DistanceProvider\Goong;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\InventoryDistanceBasedSourceSelection\Model\Convert\AddressToString;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\Data\LatLngInterfaceFactory;
use Magento\InventoryDistanceBasedSourceSelectionApi\Api\GetLatsLngsFromAddressInterface;
use Magento\InventorySourceSelectionApi\Api\Data\AddressInterface;

/**
 * @inheritdoc
 */
class GetLatsLngsFromAddress implements GetLatsLngsFromAddressInterface
{
    /**
     * @var array
     */
    private $latsLngsCache = [];

    /**
     * @var LatLngInterfaceFactory
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
     * @param AddressToString $addressToString
     * @param LatLngInterfaceFactory $latLngInterfaceFactory
     * @param GetGeoCodesForAddress|null $getGeoCodesForAddress
     */
    public function __construct(
        AddressToString $addressToString,
        LatLngInterfaceFactory $latLngInterfaceFactory,
        GetGeoCodesForAddress $getGeoCodesForAddress = null
    ) {
        $this->addressToString = $addressToString;
        $this->getGeoCodesForAddress = $getGeoCodesForAddress;
        $this->latLngInterfaceFactory = $latLngInterfaceFactory;
        $this->getGeoCodesForAddress = $getGeoCodesForAddress ?: ObjectManager::getInstance()
            ->get(GetGeoCodesForAddress::class);
    }

    /**
     * @inheritdoc
     * @throws LocalizedException
     */
    public function execute(AddressInterface $address): array
    {
        $cacheKey = $this->addressToString->execute($address);

        if (!isset($this->latsLngsCache[$cacheKey])) {
            $res = $this->getGeoCodesForAddress->execute($address);
            foreach ($res['results'] as $result) {
                $location = $result['geometry']['location'];
                $this->latsLngsCache[$cacheKey][] = $this->latLngInterfaceFactory->create([
                    'lat' => (float)$location['lat'],
                    'lng' => (float)$location['lng'],
                ]);

            }
        }

        return $this->latsLngsCache[$cacheKey];
    }
}
