<?php
namespace Magenest\NotificationBox\Controller\Customer;
use Magento\Framework\App\Action\Action;
use Magenest\NotificationBox\Helper\Helper;
use Magento\Framework\App\Action\Context;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use \Magento\Framework\UrlInterface;
use Magenest\NotificationBox\Model\CustomerNotification;
use \Psr\Log\LoggerInterface;

class GetNotificationData extends Action
{
    /** @var Helper  */
    protected $helper;

    /** @var JsonFactory  */
    protected  $resultJsonFactory;

    /** all customer notification */
    protected $notificationCollection = [];

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /** @var TimezoneInterface  */
    protected $timezoneInterface;

    /** @var UrlInterface  */
    protected $urlInterface;

    /** @var LoggerInterface  */
    protected $logger;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $collectionFactory
     * @param TimezoneInterface $timezoneInterface
     * @param UrlInterface $urlInterface
     * @param LoggerInterface $logger
     */
    public function __construct(
        Context $context,
        Helper $helper,
        JsonFactory $resultJsonFactory,
        CollectionFactory $collectionFactory,
        TimezoneInterface $timezoneInterface,
        UrlInterface $urlInterface,
        LoggerInterface $logger
    )
    {
        $this->logger                   = $logger;
        $this->urlInterface             = $urlInterface;
        $this->timezoneInterface        = $timezoneInterface;
        $this->collectionFactory        = $collectionFactory;
        $this->helper                   = $helper;
        $this->resultJsonFactory        = $resultJsonFactory;
        parent::__construct($context);
    }

    /**  */
    public function execute()
    {
        $data['customerNotLogin'] = true;
        $result = $this->resultJsonFactory->create();
        if($customerId = $this->helper->getCustomerId()){
            unset($data['customerNotLogin']);
            $data['allNotification'] = $this->getAllCustomerNotification($customerId);
            $data['unreadNotification'] = count($this->collectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->addFieldToFilter('status',CustomerNotification::STATUS_UNREAD));
        }
        return $result->setData($data);
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getAllCustomerNotification($customerId)
    {
        try {
            $this->notificationCollection = $this->collectionFactory->create()
                ->addFieldToFilter('customer_id', $customerId)
                ->setPageSize($this->helper->getMaximumNotificationOnNotificationBox())
                ->setOrder('entity_id','DESC')
                ->getData();
            $maximumCharacter = $this->helper->getMaximumNotificationDescription();
            foreach ($this->notificationCollection as & $notification){
                if($notification['status']){
                    $notification['markAsRead'] = $this->helper->getThemeColor();
                }
                else{
                    $notification['markAsRead'] = $this->helper->getUnreadNotification();
                }
                $notification['icon'] = $this->helper->getImageByNotificationType($notification);
                if(isset($notification['description'])){
                    $notification['description'] = strlen($notification['description']) <= $maximumCharacter ?$notification['description']:mb_substr($notification['description'], 0, $maximumCharacter, 'UTF-8')."...";
                }
                $notification['created_at'] = $this->timezoneInterface->formatDateTime($notification['created_at'],2,2);
                $notification['redirect_url'] = $this->urlInterface->getUrl('notibox/handleNotification/viewNotification').'?id='.$notification['entity_id'];
            }
        }
        catch (\Exception $exception){
            $this->logger->error($exception->getMessage());
        }
        return $this->notificationCollection;
    }
}
