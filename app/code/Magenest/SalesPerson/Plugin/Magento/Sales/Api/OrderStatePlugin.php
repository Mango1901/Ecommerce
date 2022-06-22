<?php

namespace Magenest\SalesPerson\Plugin\Magento\Sales\Api;

use Magento\Sales\Model\Order;
use Magento\User\Model\UserFactory as UserFactory;

class OrderStatePlugin
{
    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * OrderStatePlugin constructor.
     * @param UserFactory $userFactory
     */
    public function __construct(UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
    }

    public function afterSave(
        \Magento\Sales\Api\OrderRepositoryInterface $subject,
        $result,
        $entity
    ) {
        if ($result->getState() == Order::STATE_COMPLETE || $result->getState() == Order::STATE_CANCELED) {
            if (!empty($result->getData("assigned_to"))) {
                $adminUserCollection = $this->userFactory->create()->load($result->getData("assigned_to"));
                $adminUserCollection->setData("no_order", $adminUserCollection->getData("no_order") - 1);
                $adminUserCollection->save();
            }
        }

        return $result;
    }
}
