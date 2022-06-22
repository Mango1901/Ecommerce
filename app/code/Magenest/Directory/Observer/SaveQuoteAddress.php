<?php
namespace Magenest\Directory\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Registry;

class SaveQuoteAddress implements ObserverInterface
{

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function execute(Observer $observer)
    {
        $address = $observer->getEvent()->getQuoteAddress();
        $data    = $this->registry->registry('address_data');
        if (!empty($data)) {
            $address->setData($data);
        }
        if (!empty($address->getCityId()) && !empty($address->getDistrictId()) && !empty($address->getWardId())) {
            $address->setCityId(preg_replace("/[^0-9]/", "", $address->getCityId()));
            $address->setDistrictId(preg_replace("/[^0-9]/", "", $address->getDistrictId()));
            $address->setWardId(preg_replace("/[^0-9]/", "", $address->getWardId()));
            $district = str_replace("\n", "", $address->getDistrict());
            $district = str_replace("district", "", $district);
            $address->setDistrict($district);
            $ward = str_replace("\n", "", $address->getWard());
            $ward = str_replace("ward", "", $ward);
            $address->setWard($ward);

            $customAttr = ['city_id', 'district', 'district_id', 'ward', 'ward_id'];
            foreach ($customAttr as $attr) {
                if ($address->getCustomAttribute($attr)) {
                    $address->getCustomAttribute($attr)->setValue($address->getData($attr));
                }
            }
        }
    }
}
