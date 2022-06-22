<?php
namespace Magenest\RewardPoints\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class ShopingCartRule implements ArrayInterface {
    public function toOptionArray()
    {
        $arr = $this->getAvaiableCouponRule();
        return $arr;
    }
    public function getAvaiableCouponRule(){
        $saleRuleCollection = \Magento\Framework\App\ObjectManager::getInstance()->create('Magento\SalesRule\Model\ResourceModel\Rule\Collection');
        $saleRuleCollection
            ->addFieldToFilter('coupon_type', \Magento\SalesRule\Model\Rule::COUPON_TYPE_SPECIFIC)
            ->addFieldToFilter('use_auto_generation', 1)
            ->addFieldToFilter('is_active', 1);
        $data = array(0 => "--Please Select--");
        foreach ($saleRuleCollection->getItems() as $item){
            $data[] = [
                'value' => $item->getRuleId(),
                'label' => $item->getName()
            ];
        }
        return $data;
    }
}
