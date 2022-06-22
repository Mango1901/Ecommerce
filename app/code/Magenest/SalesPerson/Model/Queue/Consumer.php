<?php

namespace Magenest\SalesPerson\Model\Queue;

use Magento\User\Model\UserFactory as UserFactory;

class Consumer
{
    /**
     * @var UserFactory
     */
    protected $_userFactory;
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * Consumer constructor.
     * @param \Magento\Sales\Model\Order $orderModel
     * @param UserFactory $userFactory
     */
    public function __construct(
        \Magento\Sales\Model\Order $orderModel,
        UserFactory $userFactory
    ) {
        $this->_order = $orderModel;
        $this->_userFactory = $userFactory;
    }

    /**
     * @param $data
     */
    public function process($data)
    {
        $dataArray = json_decode($data, true);
        if (!empty($dataArray["order_id"]) && !empty($dataArray["user_id"]) && $dataArray["user_id"] != 0) {
            $this->_order->loadByIncrementId($dataArray["order_id"])
                ->setData("assigned_to", $dataArray["user_id"])
                ->save();
            $adminUserCollection = $this->_userFactory->create()->load($dataArray["user_id"]);
            $adminUserCollection->setData("no_order", $adminUserCollection->getData("no_order") + 1);
            $adminUserCollection->save();
        }
    }
}
