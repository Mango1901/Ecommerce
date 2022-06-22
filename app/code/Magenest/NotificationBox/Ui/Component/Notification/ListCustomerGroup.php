<?php

namespace Magenest\NotificationBox\Ui\Component\Notification;

use Magenest\NotificationBox\Helper\Helper;

class ListCustomerGroup implements \Magento\Framework\Option\ArrayInterface
{
    /** @var Helper  */
    protected $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper)
    {
        $this->helper = $helper;
    }

    public function toOptionArray()
    {
        $listCustomerGroup =[];
        $customerGroup = $this->helper->getCustomerGroups();
        foreach ($customerGroup as $customer)
        {
            $listCustomerGroup[]= ['label'=>$customer['label'],'value'=> $customer['value']];
        }
        return $listCustomerGroup;
    }
}
