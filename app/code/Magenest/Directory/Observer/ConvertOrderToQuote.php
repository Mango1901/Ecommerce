<?php

namespace Magenest\Directory\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class ConvertOrderToQuote implements ObserverInterface
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(
        Registry $registry
    )
    {
        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        $orderAddresses = $order->getAddresses() ?? [];
        foreach ($orderAddresses as $item) {
            if ($item->getAddressType() == \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING) {
                $data = [
                    'city_id' => $item->getCityId(),
                    'city'    => $item->getCity(),
                    'district_id' => $item->getDistrictId(),
                    'district' => $item->getDistrict(),
                    'ward_id' => $item->getWardId(),
                    'ward' => $item->getWard()
                ];

            }
        }
        $this->registry->register('address_data',$data ?? []);

    }
}
