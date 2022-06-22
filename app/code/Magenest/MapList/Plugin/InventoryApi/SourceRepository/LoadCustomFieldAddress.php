<?php
namespace Magenest\MapList\Plugin\InventoryApi\SourceRepository;

use Magento\Framework\DataObject;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryInStorePickup\Model\Source\InitPickupLocationExtensionAttributes;
use Magento\InventoryInStorePickupApi\Api\Data\PickupLocationInterface;
use Magento\Framework\Serialize\SerializerInterface;
/**
 * Class LoadCustomFieldAddress
 * @package Magenest\MapList\Plugin\InventoryApi\SourceRepository
 */
class LoadCustomFieldAddress
{
    /**
     * @var InitPickupLocationExtensionAttributes
     */
    private $setExtensionAttributes;
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @param InitPickupLocationExtensionAttributes $setExtensionAttributes
     */
    public function __construct(
        InitPickupLocationExtensionAttributes $setExtensionAttributes,
        SerializerInterface $serializer
    ) {
        $this->setExtensionAttributes = $setExtensionAttributes;
        $this->serializer = $serializer;
    }

    /**
     * Enrich the given Source Objects with the In-Store pickup attribute
     *
     * @param SourceRepositoryInterface $subject
     * @param SourceInterface $source
     *
     * @return SourceInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ): SourceInterface {
        $cityId = $source->getData('city_id');
        $district = $source->getData('district');
        $districtId =  $source->getData('district_id');
        $ward =  $source->getData('ward');
        $wardId =  $source->getData('ward_id');
        $isVisible = $source->getData('is_visible');
        $storeMapImg = $source->getData('store_map_img');
        $extensionAttributes = $source->getExtensionAttributes();

        if(!empty($storeMapImg)) {
            try {
                $storeMapImg = $this->serializer->unserialize($storeMapImg);
                $source->setStoreMapImg($storeMapImg);
            }catch (\Throwable $e){}
        }

        $extensionAttributes
            ->setCityId((bool)$cityId)
            ->setDistrict($district)
            ->setDistrictId($districtId)
            ->setWard($ward)
            ->setWardId($wardId)
            ->setIsVisible($isVisible)
            ->setStoreMapImg($storeMapImg);

        return $source;
    }
}
