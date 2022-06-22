<?php

namespace Magenest\MapList\Observer\Source;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class CustomAddressValue implements ObserverInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * CustomAddressValue constructor.
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }
    const COLUMNS = [
        "city_id",
        "district",
        "district_id",
        "ward",
        "ward_id",
        "is_visible",
    ];

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getEvent()->getRequest()->getParam('general');
        $source = $observer->getEvent()->getSource();
        foreach (self::COLUMNS as $column){
            $source->setData($column, $request[$column]);
        }
        if(isset($request['store_map_img'])) {
            $storeMapImg = $this->serializer->serialize($request['store_map_img']);
            $source->setData('store_map_img', $storeMapImg);
        }else{
            $source->setData('store_map_img', '');
        }
    }
}
