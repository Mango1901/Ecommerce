<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Model\Config\Source;

use Magento\Framework\App\CacheInterface;

class Province implements \Magento\Framework\Option\ArrayInterface
{
    const CACHE_TAG = 'VIETTELPOST_ADDRESS_INFO';
    const CACHE_ID = 'VIETTELPOST_PROVINCE';

    protected $provinceCollectionFactory;
    protected $cache;

    public function __construct(
        \Magenest\ViettelPost\Model\ResourceModel\Province\CollectionFactory $provinceCollectionFactory,
        CacheInterface $cache
    )
    {
        $this->provinceCollectionFactory = $provinceCollectionFactory;
        $this->cache = $cache;
    }

    public function toOptionArray()
    {
        $provinceData = $this->cache->load(self::CACHE_ID);
        if(!$provinceData) {
            $provinceCollection = $this->provinceCollectionFactory->create();
            $provinceData = [];
            foreach ($provinceCollection as $province) {
                $provinceData[] = [
                    'value' => $province->getId(),
                    'label' => $province->getProvinceName(),
                    'params' => '123'
                ];
            }
            $this->cache->save(json_encode($provinceData), self::CACHE_ID, [self::CACHE_TAG]);
        }else{
            $provinceData = json_decode($provinceData, true);
        }
        return $provinceData;
    }
}
