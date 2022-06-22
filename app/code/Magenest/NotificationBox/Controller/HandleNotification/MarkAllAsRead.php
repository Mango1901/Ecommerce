<?php
namespace Magenest\NotificationBox\Controller\HandleNotification;
use Magenest\NotificationBox\Model\Notification;
use Magento\Framework\App\Action\Action;
use Magenest\NotificationBox\Helper\Helper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\CustomerNotification as CustomerNotificationModel;

class MarkAllAsRead extends Action
{
    /** @var Helper  */
    protected $helper;

    /** @var JsonFactory  */
    protected  $resultJsonFactory;

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /** @var CustomerNotification  */
    protected $customerNotification;

    /** @var CustomerNotificationFactory  */
    protected $customerNotificationFactory;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $collectionFactory
     * @param CustomerNotification $customerNotification
     * @param CustomerNotificationFactory $customerNotificationFactory
     */
    public function __construct(
        Context $context,
        Helper $helper,
        JsonFactory $resultJsonFactory,
        CollectionFactory $collectionFactory,
        CustomerNotification $customerNotification,
        CustomerNotificationFactory  $customerNotificationFactory
    )
    {
        $this->customerNotificationFactory  = $customerNotificationFactory;
        $this->customerNotification         = $customerNotification;
        $this->collectionFactory            = $collectionFactory;
        $this->helper                       = $helper;
        $this->resultJsonFactory            = $resultJsonFactory;
        parent::__construct($context);
    }

    /** save customer Token */
    public function execute()
    {
        $customerId = $this->helper->getCustomerId();
        $result = $this->resultJsonFactory->create();
        if($customerId){
            try {
                $allCustomer = $this->collectionFactory->create();
                $allCustomer->addFieldToFilter('customer_id',$customerId);
                $allCustomer->addFieldToFilter('status',CustomerNotificationModel::STATUS_READ);
                foreach ($allCustomer as $item) {
                    $item->setData('status',CustomerNotificationModel::STATUS_READ);
                    $this->customerNotification->save($item);
                }
            }
            catch (\Exception $exception){
                return $result->setData("fail");
            }
        }
        return $result->setData("success");
    }
}
