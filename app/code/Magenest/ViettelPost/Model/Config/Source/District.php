<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Model\Config\Source;

use Magento\Framework\App\CacheInterface;

class District implements \Magento\Framework\Option\ArrayInterface
{
    const CACHE_TAG = 'VIETTELPOST_ADDRESS_INFO';
    const CACHE_ID = 'VIETTELPOST_DISTRICT';

    protected $districtCollectionFactory;
    protected $cache;

    public function __construct(
        \Magenest\ViettelPost\Model\ResourceModel\District\CollectionFactory $districtCollectionFactory,
        CacheInterface $cache
    )
    {
        $this->districtCollectionFactory = $districtCollectionFactory;
        $this->cache = $cache;
    }

    public function toOptionArray()
    {
        $districtData = $this->cache->load(self::CACHE_ID);
        if(!$districtData) {
            $districtCollection = $this->districtCollectionFactory->create();
            $districtData = [];
            foreach ($districtCollection as $district) {
                $districtData[] = [
                    'value' => $district->getId(),
                    'label' => $district->getDistrictName()
                ];
            }
            $this->cache->save(json_encode($districtData), self::CACHE_ID, [self::CACHE_TAG]);
        }else{
            $districtData = json_decode($districtData, true);
        }
        return $districtData;
    }
}
