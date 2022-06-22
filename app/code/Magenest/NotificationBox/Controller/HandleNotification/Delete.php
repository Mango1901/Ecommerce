<?php
namespace Magenest\NotificationBox\Controller\HandleNotification;
use Magenest\NotificationBox\Model\CustomerNotification as CustomerNotificationModel;
use Magento\Framework\App\Action\Action;
use Magenest\NotificationBox\Helper\Helper;
use Magento\Framework\App\Action\Context;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magento\Framework\Controller\Result\JsonFactory;

class Delete extends Action
{
    /** @var Helper  */
    protected $helper;

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /** @var CustomerNotification  */
    protected $customerNotification;

    /** @var CustomerNotificationFactory  */
    protected $customerNotificationFactory;

    /** @var JsonFactory  */
    protected  $resultJsonFactory;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param CustomerNotification $customerNotification
     * @param CustomerNotificationFactory $customerNotificationFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Helper $helper,
        CustomerNotification $customerNotification,
        CustomerNotificationFactory $customerNotificationFactory,
        JsonFactory $resultJsonFactory
    )
    {
        $this->customerNotificationFactory  = $customerNotificationFactory;
        $this->customerNotification         = $customerNotification;
        $this->helper                       = $helper;
        $this->resultJsonFactory        = $resultJsonFactory;
        parent::__construct($context);
    }

    /** Delete Notification */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $customerId = $this->helper->getCustomerId();
        $result = $this->resultJsonFactory->create();
        if($customerId && $params['listNotificationSelected'] && isset($params['type'])){
            try {
                foreach ($params['listNotificationSelected'] as $notification){
                    $notificationId = $notification;
                    $notification = $this->customerNotificationFactory->create();
                    $this->customerNotification->load($notification,$notificationId);
                    if($params['type'] =='maskAsRead') {
                        $notification->setData('status', CustomerNotificationModel::STATUS_READ);
                        $this->customerNotification->save($notification);
                    }
                    if($params['type'] =='delete'){
                        $this->customerNotification->delete($notification);
                    }
                    if($params['type'] =='unstar'){
                        $notification->setData('star', CustomerNotificationModel::UNSTAR);
                        $this->customerNotification->save($notification);
                    }
                }
            }
            catch (\Exception $exception){
                $this->messageManager->addErrorMessage('Unable to delete notification');
            }
        }
        return $result->setData("");
    }
}
