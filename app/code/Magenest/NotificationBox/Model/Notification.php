<?php

namespace Magenest\NotificationBox\Model;

use Magento\Framework\Model\AbstractModel;

class Notification extends AbstractModel
{
    const ACTIVE = 1;
    const NOT_ACTIVE = 0;
    const IS_SENT = 1;
    const IS_NOT_SENT = 0;
    const REVIEW_REMINDERS = "review_reminders";
    const REVIEW_REMINDERS_LABEL = "Review reminders";

    const ORDER_STATUS_UPDATE = "order_status_update";
    const ORDER_STATUS_UPDATE_LABEL = "Order status update";

    const ABANDONED_CART_REMINDS = "abandoned_cart_reminds";
    const ABANDONED_CART_REMINDS_LABEL = "Abandoned cart reminds";

    const REWARD_POINT_REMINDS = "reward_point_reminds";
    const REWARD_POINT_REMINDS_LABEL = "Reward point reminds";

    const STORE_CREDIT_REMINDS = "store_credit_reminds";
    const STORE_CREDIT_REMINDS_LABEL = "Store credit reminds";

    const BIRTHDAY = "birthday";
    const BIRTHDAY_LABEL = "Birthday";

    const AFFILIATE_PROGRAM = "affiliate_program";
    const AFFILIATE_PROGRAM_LABEL = "Affiliate Program";

    const NEWSLETTER = "newsletter";
    const NEWSLETTER_LABEL = "Newsletter";

    const PRODUCT_WISHLIST_PROMOTIONS = "product_wishlist_promotions";
    const PRODUCT_WISHLIST_PROMOTIONS_LABEL = "Product in wishlist have promotions";

    const CUSTOMER_LOGIN = "customer_login";
    const CUSTOMER_LOGIN_LABEL = "Customer login";

    const MAINTENANCE = "maintenance";
    const MAINTENANCE_LABEL = "Suggestions for warranty, maintenance";

    const CUSTOM_TYPE = "custom_notification_type";
    const CUSTOM_TYPE_LABEL = "Custom notification type";

    const CUSTOMER_NOT_LOGGER_IN = 0;
    const ALL_STORE_VIEWS = 0;

    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\ResourceModel\Notification');
    }
}
