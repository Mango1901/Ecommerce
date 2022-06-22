<?php

namespace Magenest\NotificationBox\Helper;

use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\CustomerToken as CustomerTokenModel;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\Notification as NotificationModel;
use Magenest\NotificationBox\Model\NotificationFactory;
use Magenest\NotificationBox\Model\NotificationTypeFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\Collection as CustomerTokenCollection;
use Magenest\NotificationBox\Model\ResourceModel\Notification;
use Magenest\NotificationBox\Model\ResourceModel\Notification\Collection as NotificationCollection;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType\Collection as NotificationTypeCollection;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\ResourceModel\Customer;
//use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\ResourceModel\Group\Collection as CustomerGroup;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Data\Form\FormKey;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\MessageQueue\PublisherInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\Helper\Data as UrlHelper;
use Magento\Framework\View\Asset\Repository;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory;
use \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollection;
use \Magento\Framework\App\ResourceConnection;

class Helper extends \Magento\Framework\App\Helper\AbstractHelper
{
    private const LIST_NOTIFICATION_TYPE = [
        NotificationModel::REVIEW_REMINDERS,
        NotificationModel::ORDER_STATUS_UPDATE,
        NotificationModel::ABANDONED_CART_REMINDS,
        NotificationModel::REWARD_POINT_REMINDS,
        NotificationModel::STORE_CREDIT_REMINDS,
        NotificationModel::BIRTHDAY,
        NotificationModel::AFFILIATE_PROGRAM,
        NotificationModel::NEWSLETTER,
        NotificationModel::PRODUCT_WISHLIST_PROMOTIONS,
        NotificationModel::CUSTOMER_LOGIN,
        NotificationModel::MAINTENANCE,
    ];

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var UrlHelper
     */
    protected $urlHelper;

    /**
     * @var FormKey
     */
    protected $formKey;

    /** @var Json */
    protected $json;

    /** @var CustomerTokenFactory */
    protected $customerTokenFactory;

    /** @var CustomerToken */
    protected $customerTokenResource;

    /** @var StoreManagerInterface */
    protected $storeManagerInterface;

    /** @var string  */
    const URL_ICON_DEFAULT = 'notificationtype/icon/default.jpg';

    //General config
    const PATH_ENABLE_NOTIFICATION_BOX = 'magenest_notification_box/general/enable';
    const PATH_API_KEY = 'magenest_notification_box/general/api_key';
    const PATH_SENDER_ID = 'magenest_notification_box/general/sender_id';
    const PATH_MAXIMUM_NOTIFICATION_IN_MY_NOTIFICATION_ON_MY_ACCOUNT_PAGE = 'magenest_notification_box/general/maximum_notification_in_my_notifications_on_my_account_page';


    //Subscriptions Popup
    const PATH_ALLOW_WEB_PUSH = 'magenest_notification_box/subscription_popup/allow_web_push';
    const PATH_CONTENT_POPUP = 'magenest_notification_box/subscription_popup/content_popup';
    const PATH_TIME_SHOW_POPUP = 'magenest_notification_box/subscription_popup/time_show_popup';
    const PATH_TIME_RESEND_POPUP = 'magenest_notification_box/subscription_popup/time_resend_popup';

    //Web Push Notifications
    const PATH_MAXIMUM_NOTIFICATION = 'magenest_notification_box/web_push_notification/maximun_notification';

    //Notification Box
    const PATH_MAXIMUM_NOTIFICATION_ON_NOTIFICATION_BOX = 'magenest_notification_box/notification_box/maximum_notification_on_notification_box';
    const PATH_MAXIMUM_NOTIFICATION_DESCRIPTION = 'magenest_notification_box/notification_box/maximum_notification_description';
    const PATH_MAXIMUM_NOTIFICATION_ON_MY_NOTIFICATION_PAGE = 'magenest_notification_box/notification_box/maximum_notification_on_my_notification_page';
    const PATH_THEME_COLOR = 'magenest_notification_box/notification_box/theme_color';
    const PATH_UNREAD_NOTIFICATION_COLOR = 'magenest_notification_box/notification_box/color_unread_notification';
    const PATH_BOX_POSITION = 'magenest_notification_box/notification_box/box_position';
    const PATH_BOX_WIDTH = 'magenest_notification_box/notification_box/box_width';
    const PATH_ALLOW_CUSTOMER_DELETE_NOTIFICATION = 'magenest_notification_box/notification_box/allow_customer_delete_notification';

    //Default Image
    const PATH_DEFAULT_IMAGE_ORDER_STATUS_UPDATE = 'magenest_notification_box/default_image/order_status_update';
    const PATH_DEFAULT_IMAGE_REVIEW_REMINDER = 'magenest_notification_box/default_image/review_reminder';
    const PATH_DEFAULT_IMAGE_ABANDONED_CART = 'magenest_notification_box/default_image/abandoned_cart';

    /** @var CustomerNotificationFactory */
    protected $customerNotificationFactory;

    /** @var CustomerNotification */
    protected $customerNotificationResource;

    /** @var Session */
    protected $customerSession;

    /** @var  NotificationTypeCollection */
    protected $notificationTypeCollection;

    /** @var NotificationCollection */
    protected $notificationCollection;

    /** @var NotificationTypeFactory */
    protected $notificationTypeFactory;

    /** @var NotificationType */
    protected $notificationType;

    /** @var CustomerNotification */
    protected $customerCollection;

    /** @var CustomerTokenCollection */
    protected $customerTokenCollection;

    /** @var CustomerFactory */
    protected $customerFactory;

    /** @var Customer */
    protected $customerResource;

    /** @var NotificationFactory */
    protected $notificationFactory;

    /** @var Notification */
    protected $notificationResource;

    /** @var Repository */
    private $repository;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var CustomerGroup  */
    protected $customerGroup;

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /** @var AdapterInterface  */
    protected $connection;

    /** @var ResourceConnection  */
    protected $resource;

    /** @var PublisherInterface  */
    protected $publisher;

    /** @var string[]  */
    protected $customerNotificationColumns = [
        'notification_id',
        'customer_id',
        'status',
        'star',
        'star',
        'notification_type',
        'condition',
        'description',
        'redirect_url'
    ];

    /**
     * Data constructor.
     * @param Json $json
     * @param Context $context
     * @param UrlHelper $urlHelper
     * @param FormKey $formKey
     * @param CustomerNotificationFactory $customerNotificationFactory
     * @param CustomerNotification $customerNotification
     * @param Session $session
     * @param CustomerTokenFactory $customerTokenFactory
     * @param CustomerToken $customerNotificationResource
     * @param NotificationTypeCollection $notificationTypeCollection
     * @param NotificationCollection $notificationCollection
     * @param NotificationTypeFactory $notificationTypeFactory
     * @param NotificationType $notificationType
     * @param CustomerCollection $customerCollection
     * @param CustomerTokenCollection $customerTokenCollection
     * @param CustomerFactory $customerFactory
     * @param Customer $customerResource
     * @param StoreManagerInterface $storeManagerInterface
     * @param NotificationFactory $notificationFactory
     * @param Notification $notificationResource
     * @param Repository $repository
     * @param LoggerInterface $logger
     * @param CustomerGroup $customerGroup
     * @param CollectionFactory $collectionFactory
     * @param ResourceConnection $resource
     * @param PublisherInterface $publisher
     */
    public function __construct(
        Json $json,
        Context $context,
        UrlHelper $urlHelper,
        FormKey $formKey,
        CustomerNotificationFactory $customerNotificationFactory,
        CustomerNotification $customerNotification,
        Session $session,
        CustomerTokenFactory $customerTokenFactory,
        CustomerToken $customerNotificationResource,
        NotificationTypeCollection $notificationTypeCollection,
        NotificationCollection $notificationCollection,
        NotificationTypeFactory $notificationTypeFactory,
        NotificationType $notificationType,
        CustomerCollection $customerCollection,
        CustomerTokenCollection $customerTokenCollection,
        CustomerFactory $customerFactory,
        Customer $customerResource,
        StoreManagerInterface $storeManagerInterface,
        NotificationFactory $notificationFactory,
        Notification $notificationResource,
        Repository $repository,
        LoggerInterface $logger,
        CustomerGroup $customerGroup,
        CollectionFactory $collectionFactory,
        ResourceConnection $resource,
        PublisherInterface $publisher
    ) {
        $this->publisher = $publisher;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->customerGroup = $customerGroup;
        $this->logger = $logger;
        $this->repository = $repository;
        $this->notificationFactory = $notificationFactory;
        $this->notificationResource = $notificationResource;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->customerFactory = $customerFactory;
        $this->customerResource = $customerResource;
        $this->customerTokenCollection = $customerTokenCollection;
        $this->customerCollection = $customerCollection;
        $this->notificationType = $notificationType;
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->notificationCollection = $notificationCollection;
        $this->notificationTypeCollection = $notificationTypeCollection;
        $this->customerTokenResource = $customerNotificationResource;
        $this->customerTokenFactory = $customerTokenFactory;
        $this->json = $json;
        $this->customerSession = $session;
        $this->customerNotificationFactory = $customerNotificationFactory;
        $this->customerNotificationResource = $customerNotification;
        $this->scopeConfig = $context->getScopeConfig();
        $this->urlHelper = $urlHelper;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    //get config value by path
    protected function getConfig($path)
    {
        return $this->scopeConfig->getValue($path);
    }

    /**
     * @return mixed
     */
    public function getEnableModule()
    {
        return $this->getConfig(self::PATH_ENABLE_NOTIFICATION_BOX);
    }

    /**
     * @return mixed
     */
    public function getApiKey()
    {
        return $this->getConfig(self::PATH_API_KEY);
    }

    /**
     * @return mixed
     */
    public function getSenderId()
    {
        return $this->getConfig(self::PATH_SENDER_ID);
    }

    /**
     * @return mixed
     */
    public function getAllowWebPush()
    {
        return $this->getConfig(self::PATH_ALLOW_WEB_PUSH);
    }

    public function getContentPopup()
    {
        return $this->getConfig(self::PATH_CONTENT_POPUP);
    }

    /**
     * @return mixed
     */
    public function getTimeShowPopup()
    {
        return $this->getConfig(self::PATH_TIME_SHOW_POPUP);
    }

    /**
     * @return mixed
     */
    public function getTimeResendPopUp()
    {
        return $this->getConfig(self::PATH_TIME_RESEND_POPUP);
    }

    /**
     * @return mixed
     */
    public function getMaximumNotification()
    {
        return $this->getConfig(self::PATH_MAXIMUM_NOTIFICATION);
    }

    /**
     * @return mixed
     */
    public function getMaximumNotificationDescription()
    {
        return $this->getConfig(self::PATH_MAXIMUM_NOTIFICATION_DESCRIPTION);
    }

    /**
     * @return mixed
     */
    public function getMaximumNotificationOnMyNotificationPage()
    {
        return $this->getConfig(self::PATH_MAXIMUM_NOTIFICATION_ON_MY_NOTIFICATION_PAGE);
    }

    /**
     * @return mixed
     */
    public function getMaximumNotificationOnNotificationBox()
    {
        return $this->getConfig(self::PATH_MAXIMUM_NOTIFICATION_ON_NOTIFICATION_BOX);
    }

    /**
     * @return mixed
     */
    public function getMaximumNotificationInMyNotificationOnMyAccountPage()
    {
        return $this->getConfig(self::PATH_MAXIMUM_NOTIFICATION_IN_MY_NOTIFICATION_ON_MY_ACCOUNT_PAGE);
    }

    /**
     * @return mixed
     */
    public function getThemeColor()
    {
        return $this->getConfig(self::PATH_THEME_COLOR);
    }

    public function getUnreadNotification()
    {
        return $this->getConfig(self::PATH_UNREAD_NOTIFICATION_COLOR);
    }

    /**
     * @return mixed
     */
    public function getBoxPosition()
    {
        return $this->getConfig(self::PATH_BOX_POSITION);
    }

    /**
     * @return mixed
     */
    public function getBoxWidth()
    {
        return $this->getConfig(self::PATH_BOX_WIDTH);
    }

    /**
     * @return mixed
     */
    public function getAllowCustomerDeleteNotification()
    {
        return $this->getConfig(self::PATH_ALLOW_CUSTOMER_DELETE_NOTIFICATION);
    }

    /**
     * @return mixed
     */
    public function getDefaultImageOrderStatusUpdate()
    {
        return $this->getConfig(self::PATH_DEFAULT_IMAGE_ORDER_STATUS_UPDATE);
    }

    /**
     * @return mixed
     */
    public function getDefaultImageReviewReminder()
    {
        return $this->getConfig(self::PATH_DEFAULT_IMAGE_REVIEW_REMINDER);
    }

    /**
     * @return mixed
     */
    public function getDefaultImageAbandonedCart()
    {
        return $this->getConfig(self::PATH_DEFAULT_IMAGE_ABANDONED_CART);
    }

    public function getCustomerNotification()
    {
        $customerId = $this->getCustomerId();
        if ($customerId) {
            $customerNotificationModel = $this->customerNotificationFactory->create();
            $this->customerNotificationResource->load($customerNotificationModel, 'customer_id', $customerId);
            return $customerNotificationModel;
        }
        return null;
    }

    /** get customer Id */
    public function getCustomerId()
    {
        $customer = $this->customerSession;
        return $customer ? $customer->getId() : null;
    }

    public function getCustomerName()
    {
        $customer = $this->customerSession;
        return $customer ? $customer->getCustomer()->getName() : null;
    }

    //get all customer
    public function getAllCustomer()
    {
        return $this->customerCollection;
    }

    public function getBaseUrl()
    {
        return $this->getBaseUrl();
    }

    //send notification in magento
    public function sendNotificationInMagento($notification)
    {
        $currentPage = 1;
        $allCustomer = $this->customerCollection->create()->setPageSize(5000);
        $totalPage = $allCustomer->getLastPageNumber();
        $tableName = $this->resource->getTableName('magenest_customer_notification');
        $listCustomerGroup = $this->json->unserialize($notification['customer_group']);
        $listStore = $this->json->unserialize($notification['store_view']);
        while ($currentPage <= $totalPage){
            $data = [];
            $allCustomerClone = clone $allCustomer;
            $CustomerPart = $allCustomerClone->setCurPage($currentPage)->getData();
            foreach ($CustomerPart as $customer) {
                $customerId = $customer['entity_id'];
                $customerGroupId = $customer['group_id'];
                $customerStoreId = $customer['store_id'];

                //remove notification if not meet conditions
                if (!in_array('0', $listStore) && !in_array($customerStoreId, $listStore)) {
                    continue;
                }
                if (!in_array($customerGroupId, $listCustomerGroup)) {
                    continue;
                }
                $finalNotification = $notification;
                $finalNotification['customer_id'] = $customerId;
                $finalNotification['star'] = 0;
                $finalNotification['status'] = 0;
                $finalNotification['notification_id'] = $notification['id'];

                $finalNotification = $this->unsetDataBeforeSave($finalNotification);
                $data[] = $finalNotification;
            }
            $this->connection->insertMultiple($tableName, $data);
            $currentPage++;
        }
    }

    /**
     * handle notification data before save
     * @param $data
     * @return mixed
     */
    public function unsetDataBeforeSave($data){
        foreach ($data as $key=>$value){
            if(!in_array($key,$this->customerNotificationColumns)){
                unset($data[$key]);
            }
        }
        return $data;
    }

    //send notification via firebase
    public function sendNotificationWithFireBase($notification, $token = null)
    {
        $allCustomerToken = [];
        $listStore = $this->json->unserialize($notification['store_view']);
        try {
            //get all token
            if (isset($token)) {
                //only send notification to 1 token
                if ($token->getIsActive()) {
                    $allCustomerToken = ['token' => $token->getData()];
                }
            } else {
                //send to all token
                $allCustomerToken = $this->customerTokenCollection->addFieldToFilter('status', CustomerTokenModel::STATUS_SUBSCRIBED)
                    ->addFieldToFilter('is_active', CustomerTokenModel::IS_ACTIVE);
                if(!in_array('0', $listStore)){
                    $allCustomerToken->addFieldToFilter('store_id', ['in'=> $listStore]);
                }
                $allCustomerToken = $allCustomerToken->getData();
            }
            $maximumNumberNotificationPerDay = $this->getMaximumNotification();
            if (count($allCustomerToken)) {

                $listCustomerGroup = $this->json->unserialize($notification['customer_group']);
                $isSent = [];
                foreach ($allCustomerToken as $token) {
                    //prevent sending multiple same notification to 1 customer
                    if (in_array($token['token'], $isSent)) {
                        continue;
                    }
                    $limit = $this->getLimitNotification($token);
                    if ($limit < $maximumNumberNotificationPerDay) {
                        if (!in_array('0', $listStore) && !in_array($token['store_id'], $listStore)) {
                            continue;
                        }
                        //customer token
                        if (isset($token['customer_id'])) {
                            $customer = $this->customerFactory->create();
                            $this->customerResource->load($customer, $token['customer_id']);
                            $customerGroupId = $customer->getGroupId();

                            if (!in_array($customerGroupId, $listCustomerGroup)) {
                                continue;
                            }
                        }
                        //guest token
                        else {
                            if (!in_array('0', $listCustomerGroup)) {
                                continue;
                            }
                        }
                        $urlImage = $this->getImageByNotificationType($notification);
                        $baseUrl = $this->storeManagerInterface->getStore()->getBaseUrl() . "notibox/handleNotification/clickToNotification?notificationId=" . $notification['id'] . "&url=";
                        $url = isset($notification['redirect_url']) ? $notification['redirect_url'] : "";
                        $data = [
                            "notification" => [
                                "title" => $notification['name'],
                                "body" => $notification['description'],
                                "icon" => $urlImage,
                                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
                            ],
                            "to" => $token['token'],
                            "data" => [
                                "id" =>$notification['id'],
                                "title" => $notification['name'],
                                "body" => $notification['description'],
                                "icon" => $urlImage,
                                "click_action" => $baseUrl . $url
                            ]
                        ];
                        $data_string = json_encode($data);
                        $this->sendNotification($data_string, $token['token'], $notification['id']);
                        $isSent[] = $token['token'];
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error('There was an error sending notifications via firebase :' . $exception->getMessage());
        }
    }

    //send notification via firebase
    public function sendNotification($data_string, $token, $notificationId)
    {
        $serverKey = $this->getApiKey();
        $headers = ['Authorization: key=' . $serverKey, 'Content-Type: application/json'];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $response = curl_exec($ch);
        try {
            $response = $this->json->unserialize($response);
        } catch (\Exception $exception) {
            $response = null;
        }
        $tokenModel = $this->customerTokenFactory->create();
        $this->customerTokenResource->load($tokenModel, $token, 'token');
        if (isset($response['success']) && $response['success'] == 1) {
            //update total sent
            $notificationModel = $this->notificationFactory->create();
            $this->notificationResource->load($notificationModel, $notificationId);
            $notificationModel->setData('total_sent', $notificationModel->getTotalSent() + 1);
            //update total click
            $this->notificationResource->save($notificationModel);
            $tokenModel->setData('limit', $tokenModel->getLimit() + 1);
            $this->customerTokenResource->save($tokenModel);
        } elseif (isset($response['success']) && $response['success'] == 0) {
            //delete token if not unavailable
            $this->customerTokenResource->delete($tokenModel);
        }

        curl_close($ch);
    }

    /**
     * @param $token
     * @return CustomerTokenModel
     */
    public function getTokenDataByToken($token)
    {
        $tokenModel = $this->customerTokenFactory->create();
        $this->customerNotificationResource->load($tokenModel, $token, 'token');
        return $tokenModel;
    }

    /**
     * @param $condition
     * @return NotificationCollection
     */
    public function getAllNotificationByCondition($condition)
    {
        return $this->notificationCollection->addFieldToFilter('condition', $condition);
    }

    /**
     * @param $notification
     * @return mixed|string
     */
    public function getImageByNotificationType($notification)
    {
        $image = $this->getImageDefault();
        $notificationModel = $this->notificationTypeFactory->create();
        if (in_array($notification['notification_type'], self::LIST_NOTIFICATION_TYPE)) {
            $this->notificationType->load($notificationModel, $notification['notification_type'], 'default_type');
        } else {
            $this->notificationType->load($notificationModel, $notification['notification_type'], 'entity_id');
        }
        if ($notificationModel->getIcon()) {
            try {
                $image = $this->json->unserialize($notificationModel->getIcon());
                $image = $image[0]['url'];
            } catch (\Exception $exception) {
                return $image;
            }
        }
        return $image;
    }

    /**
     * @param $notificationId
     * @return mixed
     */
    public function getNotificationNameById($notificationId){
        $notificationModel = $this->notificationFactory->create();
        $this->notificationResource->load($notificationModel,$notificationId);
        return $notificationModel->getName();
    }

    /**
     * get default notification type image
     */
    public function getDefaultImage()
    {
        return $listDefaultImage = [
            NotificationModel::REVIEW_REMINDERS => '/review-reminder.svg',
            NotificationModel::ABANDONED_CART_REMINDS =>  '/abandoned-cart-reminder.svg',
            NotificationModel::ORDER_STATUS_UPDATE =>  '/order-status-update.svg',
            NotificationModel::STORE_CREDIT_REMINDS =>  '/store_credit_reminds.svg',
            NotificationModel::REWARD_POINT_REMINDS =>  '/reward_point_reminds.svg',
            NotificationModel::BIRTHDAY =>  '/birthday.svg',
            NotificationModel::AFFILIATE_PROGRAM =>  '/affiliate_program.svg',
            NotificationModel::NEWSLETTER =>  '/newsletter.svg',
            NotificationModel::PRODUCT_WISHLIST_PROMOTIONS =>  '/product_wishlist_promotions.svg',
            NotificationModel::CUSTOMER_LOGIN =>  '/customer_login.svg',
            NotificationModel::MAINTENANCE =>  '/maintenance.svg',
        ];
    }

    /**
     * get customer group
     */
    public function getCustomerGroups()
    {
        return $this->customerGroup->toOptionArray();
    }

    /**
     * @param $src
     * @param $dst
     */
    public function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        if (!file_exists($dst)) {
            mkdir($dst);
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    recurse_copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * find the firebase-messaging-sw.js file and copy to root folder
     * @param $src
     * @param $dst
     */
    public function copyFile($src, $dst)
    {
        //open media folder
        $dir = opendir($src);
        //find the firebase-messaging-sw.js file and copy to root folder
        while (false !== ($file = readdir($dir))) {
            if ($file == 'firebase-messaging-sw.js') {
                copy($src . '/' . $file, $dst . '/' . $file);
                break;
            }
        }
        closedir($dir);
    }

    /**
     * @param $notification
     * @return CustomerTokenCollection
     */
    public function getToken($notification)
    {
        return $this->collectionFactory->create()->addFieldToFilter('customer_id',$notification['customer_id'])
                        ->addFieldToFilter('is_active', CustomerTokenModel::IS_ACTIVE);
    }

    /** get default notification image when notification type image not exist*/
    public function getImageDefault()
    {
        $currentStore = $this->storeManagerInterface->getStore();
        return $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::URL_ICON_DEFAULT;
    }

    /**
     * Get total number of notifications sent for 1 token
     * @param $token
     * @return int
     */
    public function getLimitNotification($token){
        $total = 0;
        $customerToken = $this->collectionFactory->create()->addFieldToFilter('guest_id',$token['guest_id'])
            ->addFieldToFilter('token',$token['token']);
        foreach ($customerToken as $token){
            $total += $token['limit'];
        }
        return $total;
    }
}
