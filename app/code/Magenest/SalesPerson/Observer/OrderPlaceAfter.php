<?php

namespace Magenest\SalesPerson\Observer;

use Magenest\SalesPerson\Helper\AssignedToSales;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\User\Model\ResourceModel\User\Collection as UserCollection;

/**
 * Class OrderPlaceAfter
 * @package Magenest\Affiliate\Observer
 */
class OrderPlaceAfter implements ObserverInterface
{
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var AssignedToSales
     */
    protected $_assignHelper;
    /**
     * @var UserCollection
     */
    protected $userCollection;

    /**
     * OrderPlaceAfter constructor.
     * @param UserCollection $userCollection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param ResourceConnection $resourceConnection
     * @param AssignedToSales $assignHelper
     */
    public function __construct(
        UserCollection $userCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ResourceConnection $resourceConnection,
        AssignedToSales $assignHelper
    ) {
        $this->userCollection = $userCollection;
        $this->_assignHelper = $assignHelper;
        $this->scopeConfig = $scopeConfig;
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * @param Observer $observer
     *
     * @return mixed
     * @throws InputException
     * @throws FailureToSendException
     */
    public function execute(Observer $observer)
    {
        $userIds = [];
        $connection = $this->resourceConnection->getConnection();
        $resultCollection = $this->userCollection->addFieldToSelect("user_id")
            ->addFieldToSelect("no_order")
            ->addFieldToFilter("is_active", 1)
            ->addFieldToFilter("is_salesperson", 1)
            ->getSelect()->joinLeft(
                ['aus' => $connection->getTableName('admin_user_session')],
                'aus.user_id = main_table.user_id',
                ["updated_at" => 'MAX(aus.updated_at)']
            )->group("user_id");
        $results = $connection->fetchAll($resultCollection);
        foreach ($results as $result) {
            if (strtotime($result["updated_at"]) + $this->scopeConfig->getValue("system/security/max_session_size_admin") > strtotime(date("Y-m-d H:i:s"))) {
                $userIds[] = [
                    "no_order" => $result['no_order'],
                    "user_id" => $result["user_id"]
                ];
            }
        }
        $order = $observer->getEvent()->getOrder();

        if (!empty($userIds)) {
            $getUserIds = min($userIds);

            $data = [
                "user_id" => $getUserIds["user_id"],
                "order_id" => $order->getData("increment_id")
            ];
            $this->_assignHelper->assignQueueData($data);
        }

        return $this;
    }
}
