<?php

namespace Magenest\NotificationBox\Controller\Adminhtml\NotificationType;

use Exception;
use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\Notification as NotificationModel;
use Magenest\NotificationBox\Model\NotificationTypeFactory;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType\CollectionFactory;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class RestoreDefaultNotification
 * @package Magenest\NotificationBox\Controller\Adminhtml\NotificationType
 */
class RestoreDefaultNotification extends Action
{
    const URL_ICON = 'notificationtype/icon';

    /** @var NotificationType */
    protected $notificationType;

    /** @var NotificationTypeFactory */
    protected $notificationTypeFactory;

    /** @var Json */
    protected $serialize;

    /** @var CollectionFactory */
    protected $collectionFactory;

    /** @var StoreManagerInterface */
    protected $storeManagerInterface;

    /** @var Helper */
    private $helper;

    /**
     * @param Action\Context $context
     * @param NotificationTypeFactory $notificationTypeFactory
     * @param NotificationType $notificationType
     * @param Json $serialize
     * @param CollectionFactory $collectionFactory
     * @param StoreManagerInterface $storeManagerInterface
     * @param Helper $helper
     */
    public function __construct(
        Action\Context          $context,
        NotificationTypeFactory $notificationTypeFactory,
        NotificationType        $notificationType,
        Json                    $serialize,
        CollectionFactory       $collectionFactory,
        StoreManagerInterface   $storeManagerInterface,
        Helper                  $helper
    ) {
        parent::__construct($context);
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->notificationType = $notificationType;
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->serialize = $serialize;
        $this->collectionFactory = $collectionFactory;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->helper = $helper;
    }


    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $currentStore = $this->storeManagerInterface->getStore();
            $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . self::URL_ICON;
            $listDefaultImage = $this->helper->getDefaultImage();
            $listDefaultNotificationType = [
                NotificationModel::ABANDONED_CART_REMINDS => NotificationModel::ABANDONED_CART_REMINDS,
                NotificationModel::REVIEW_REMINDERS => NotificationModel::REVIEW_REMINDERS,
                NotificationModel::ORDER_STATUS_UPDATE => NotificationModel::ORDER_STATUS_UPDATE,
                NotificationModel::STORE_CREDIT_REMINDS => NotificationModel::STORE_CREDIT_REMINDS,
                NotificationModel::REWARD_POINT_REMINDS => NotificationModel::REWARD_POINT_REMINDS,
                NotificationModel::BIRTHDAY => NotificationModel::BIRTHDAY,
                NotificationModel::AFFILIATE_PROGRAM => NotificationModel::AFFILIATE_PROGRAM,
                NotificationModel::NEWSLETTER => NotificationModel::NEWSLETTER,
                NotificationModel::PRODUCT_WISHLIST_PROMOTIONS => NotificationModel::PRODUCT_WISHLIST_PROMOTIONS,
                NotificationModel::CUSTOMER_LOGIN => NotificationModel::CUSTOMER_LOGIN,
            ];
            $listExistDefaultNotificationType = $this->collectionFactory->create()
                ->addFieldToFilter('default_type', array("in" => array($listDefaultNotificationType)));

            foreach ($listExistDefaultNotificationType as $notificationType) {
                unset($listDefaultNotificationType[$notificationType->getDefaultType()]);
            }
            $totalRestore = 0;
            foreach ($listDefaultNotificationType as $notificationType) {
                if ($notificationType == NotificationModel::REVIEW_REMINDERS) {
                    $this->addReviewReminderNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::ORDER_STATUS_UPDATE) {
                    $this->addOrderStatusUpdateNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::ABANDONED_CART_REMINDS) {
                    $this->addAbandonedCartNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::STORE_CREDIT_REMINDS) {
                    $this->addStoreCreditRemindsNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::REWARD_POINT_REMINDS) {
                    $this->addRewardPointRemindsNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::BIRTHDAY) {
                    $this->addBirthdayNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::AFFILIATE_PROGRAM) {
                    $this->addAffiliateProgramNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::NEWSLETTER_LABEL) {
                    $this->addNewsletterNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::PRODUCT_WISHLIST_PROMOTIONS) {
                    $this->addProductWishlistPromotionsNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::CUSTOMER_LOGIN) {
                    $this->addCustomerLoginNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                } elseif ($notificationType == NotificationModel::MAINTENANCE) {
                    $this->addMaintenanceNotificationType($mediaUrl, $listDefaultImage);
                    $totalRestore++;
                }
            }
            $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been restored.', $totalRestore));

        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addReviewReminderNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::REVIEW_REMINDERS_LABEL,
            'description' => NotificationModel::REVIEW_REMINDERS_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::REVIEW_REMINDERS,
            'icon' => '[{
                                "name": "' . NotificationModel::REVIEW_REMINDERS . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::REVIEW_REMINDERS] . '",
                                "size":"718"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $data
     * @throws AlreadyExistsException
     */
    private function saveNotificationType($data)
    {
        $model = $this->notificationTypeFactory->create();
        $model->addData($data);
        $this->notificationType->save($model);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addOrderStatusUpdateNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::ORDER_STATUS_UPDATE_LABEL,
            'description' => NotificationModel::ORDER_STATUS_UPDATE_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::ORDER_STATUS_UPDATE,
            'icon' => '[{
                                "name": "' . NotificationModel::ORDER_STATUS_UPDATE . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::ORDER_STATUS_UPDATE] . '",
                                "size":"1474"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addMaintenanceNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::MAINTENANCE_LABEL,
            'description' => NotificationModel::MAINTENANCE_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::MAINTENANCE,
            'icon' => '[{
                                "name": "' . NotificationModel::MAINTENANCE . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::MAINTENANCE] . '",
                                "size":"1474"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addCustomerLoginNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::CUSTOMER_LOGIN_LABEL,
            'description' => NotificationModel::CUSTOMER_LOGIN_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::CUSTOMER_LOGIN,
            'icon' => '[{
                                "name": "' . NotificationModel::CUSTOMER_LOGIN . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::CUSTOMER_LOGIN] . '",
                                "size":"1474"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addProductWishlistPromotionsNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::PRODUCT_WISHLIST_PROMOTIONS_LABEL,
            'description' => NotificationModel::PRODUCT_WISHLIST_PROMOTIONS_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::PRODUCT_WISHLIST_PROMOTIONS,
            'icon' => '[{
                                "name": "' . NotificationModel::PRODUCT_WISHLIST_PROMOTIONS . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::PRODUCT_WISHLIST_PROMOTIONS] . '",
                                "size":"1474"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addAffiliateProgramNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::AFFILIATE_PROGRAM_LABEL,
            'description' => NotificationModel::AFFILIATE_PROGRAM_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::AFFILIATE_PROGRAM,
            'icon' => '[{
                                "name": "' . NotificationModel::AFFILIATE_PROGRAM . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::AFFILIATE_PROGRAM] . '",
                                "size":"1474"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addAbandonedCartNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::ABANDONED_CART_REMINDS_LABEL,
            'description' => NotificationModel::ABANDONED_CART_REMINDS_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::ABANDONED_CART_REMINDS,
            'icon' => '[{
                                "name": "' . NotificationModel::ABANDONED_CART_REMINDS . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::ABANDONED_CART_REMINDS] . '",
                                "size":"1093"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addRewardPointRemindsNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::REWARD_POINT_REMINDS_LABEL,
            'description' => NotificationModel::REWARD_POINT_REMINDS_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::REWARD_POINT_REMINDS,
            'icon' => '[{
                                "name": "' . NotificationModel::REWARD_POINT_REMINDS . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::REWARD_POINT_REMINDS] . '",
                                "size":"1093"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addStoreCreditRemindsNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::STORE_CREDIT_REMINDS_LABEL,
            'description' => NotificationModel::STORE_CREDIT_REMINDS_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::STORE_CREDIT_REMINDS,
            'icon' => '[{
                                "name": "' . NotificationModel::STORE_CREDIT_REMINDS . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::STORE_CREDIT_REMINDS] . '",
                                "size":"1093"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addBirthdayNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::BIRTHDAY_LABEL,
            'description' => NotificationModel::BIRTHDAY_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::BIRTHDAY,
            'icon' => '[{
                                "name": "' . NotificationModel::BIRTHDAY . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::BIRTHDAY] . '",
                                "size":"1093"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    /**
     * @param $mediaUrl
     * @param $listDefaultImage
     * @throws AlreadyExistsException
     */
    private function addNewsletterNotificationType($mediaUrl, $listDefaultImage)
    {
        $data = [
            'name' => NotificationModel::NEWSLETTER_LABEL,
            'description' => NotificationModel::NEWSLETTER_LABEL,
            'is_category' => 1,
            'default_type' => NotificationModel::NEWSLETTER,
            'icon' => '[{
                                "name": "' . NotificationModel::NEWSLETTER . '",
                                "type": "image/png",
                                "url": "' . $mediaUrl . $listDefaultImage[NotificationModel::NEWSLETTER] . '",
                                "size":"1093"
                    }]'
        ];
        $this->saveNotificationType($data);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_NotificationBox::notification_type');
    }
}
