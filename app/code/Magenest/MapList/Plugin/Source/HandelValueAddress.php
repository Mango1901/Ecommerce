<?php

namespace Magenest\MapList\Plugin\Source;

/**
 * Class HandelValueAddress
 * @package Magenest\MapList\Plugin\Source
 */
class HandelValueAddress
{
    /**
     * @var \Magento\Inventory\Model\SourceRepository
     */
    protected $sourceRepository;

    /**
     * HandelValueAddress constructor.
     * @param \Magento\Inventory\Model\SourceRepository $sourceRepository
     */
    public function __construct(\Magento\Inventory\Model\SourceRepository $sourceRepository)
    {
        $this->sourceRepository = $sourceRepository;
    }

    /**
     * @param \Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetData(\Magento\InventoryAdminUi\Ui\DataProvider\SourceDataProvider $subject, $result)
    {
        if(!isset($result['totalRecords'])){
            foreach ($result as $code => $value){
                $source = $this->sourceRepository->get($code);
                if($source->getCityId()){
                    $result[$code]['general']['city_id'] = $source->getCityId();
                    $result[$code]['general']['district_id'] = $source->getDistrictId();
                    $result[$code]['general']['district'] = $source->getDistrict();
                    $result[$code]['general']['ward'] = $source->getWard();
                    $result[$code]['general']['ward_id'] = $source->getWardId();
                    $result[$code]['general']['is_visible'] = $source->getIsVisible();
                    $result[$code]['general']['store_map_img'] = $source->getStoreMapImg();
                }
            }
        }
        return $result;
    }

}
