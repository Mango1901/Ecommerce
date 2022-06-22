<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Model\Config\Source;

use Magento\Framework\App\CacheInterface;

class Wards implements \Magento\Framework\Option\ArrayInterface
{
    const CACHE_TAG = 'VIETTELPOST_ADDRESS_INFO';
    const CACHE_ID = 'VIETTELPOST_WARDS';

    protected $wardsCollectionFactory;
    protected $cache;

    public function __construct(
        \Magenest\ViettelPost\Model\ResourceModel\Wards\CollectionFactory $wardsCollectionFactory,
        CacheInterface $cache
    )
    {
        $this->wardsCollectionFactory = $wardsCollectionFactory;
        $this->cache = $cache;
    }

    public function toOptionArray()
    {
        $wardsData = $this->cache->load(self::CACHE_ID);
        if(!$wardsData) {
            $wardsCollection = $this->wardsCollectionFactory->create();
            $wardsData = [];
            foreach ($wardsCollection as $wards) {
                $wardsData[] = [
                    'value' => $wards->getId(),
                    'label' => $wards->getWardsName()
                ];
            }
            $this->cache->save(json_encode($wardsData), self::CACHE_ID, [self::CACHE_TAG]);
        }else{
            $wardsData = json_decode($wardsData, true);
        }
        return $wardsData;
    }
}
