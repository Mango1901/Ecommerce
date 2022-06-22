<?php

namespace Magenest\NotificationBox\Ui\Component\Listing\Columns;

use Exception;
use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\Notification;
use Magenest\NotificationBox\Model\Notification as NotificationModel;
use Magenest\NotificationBox\Model\NotificationTypeFactory;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Image
 * @package Magenest\NotificationBox\Ui\Component\Listing\Columns
 */
class Image extends Column
{
    const LIST_NOTIFICATION_TYPE = [
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

    const NAME = 'image';

    const ALT_FIELD = 'name';

    /** @var string default icon directory url */

    /** @var \Magento\Catalog\Helper\Image */
    protected $imageHelper;

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var Json */
    protected $serialize;

    /** @var Helper */
    protected $helper;

    /** @var StoreManagerInterface */
    protected $storeManagerInterface;

    /** @var NotificationTypeFactory */
    protected $notificationTypeFactory;

    /** @var NotificationType */
    protected $notificationTypeResource;

    /**
     * @param NotificationTypeFactory $notificationTypeFactory
     * @param NotificationType $notificationTypeResource
     * @param StoreManagerInterface $storeManagerInterface
     * @param Helper $helper
     * @param Json $serialize
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        NotificationTypeFactory       $notificationTypeFactory,
        NotificationType              $notificationTypeResource,
        StoreManagerInterface         $storeManagerInterface,
        Helper                        $helper,
        Json                          $serialize,
        ContextInterface              $context,
        UiComponentFactory            $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        UrlInterface                  $urlBuilder,
        array                         $components = [],
        array                         $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->serialize = $serialize;
        $this->helper = $helper;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->notificationTypeResource = $notificationTypeResource;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getName();
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['notification_type'])) {
                    $notificationModel = $this->notificationTypeFactory->create();
                    if (in_array($item['notification_type'], self::LIST_NOTIFICATION_TYPE)) {
                        $this->notificationTypeResource->load($notificationModel, $item['notification_type'], 'default_type');
                        $item['notification_type'] = $notificationModel->getName();
                    } else {
                        $this->notificationTypeResource->load($notificationModel, $item['notification_type'], 'entity_id');
                        $item['notification_type'] = $notificationModel->getName();
                    }
                    if ($notificationModel->getIcon()) {
                        try {
                            $image = $this->serialize->unserialize($notificationModel->getIcon());
                            $item[$fieldName . '_src'] = $image[0]['url'];
                            $item[$fieldName . '_orig_src'] = $image[0]['url'];

                        } catch (Exception $e) {
                            $item['image'] = '';
                        }
                    } else {
                        $mediaUrl = $this->helper->getImageDefault();
                        $item[$fieldName . '_src'] = $mediaUrl;
                        $item[$fieldName . '_orig_src'] = $mediaUrl;
                    }
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'notibox/notification/newAction', ['id' => $item['id']]
                    );
                }
            }
        }
        return $dataSource;
    }
}
