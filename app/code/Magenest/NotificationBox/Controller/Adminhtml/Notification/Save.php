<?php

namespace Magenest\NotificationBox\Controller\Adminhtml\Notification;

use Exception;
use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\Notification as NotificationModel;
use Magenest\NotificationBox\Model\NotificationFactory;
use Magenest\NotificationBox\Model\ResourceModel\Notification;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\DateTime as DateTime;

/**
 * Class Save
 * @package Magenest\InstagramShop\Controller\Adminhtml\Hotspot
 */
class Save extends Action
{
    private const LIST_NOTIFICATION_TYPE = [
        NotificationModel::REVIEW_REMINDERS,
        NotificationModel::ORDER_STATUS_UPDATE,
        NotificationModel::ABANDONED_CART_REMINDS,
        NotificationModel::REWARD_POINT_REMINDS,
        NotificationModel::STORE_CREDIT_REMINDS,
        NotificationModel::BIRTHDAY
    ];

    /** @var Notification */
    protected $notification;

    /** @var RedirectFactory */
    protected $resultRedirectFactory;

    /** @var Json */
    protected $serialize;

    /**
     * @var NotificationFactory
     */
    protected $notificationFactory;

    /** @var Helper */
    protected $helper;

    /** @var DateTime */
    protected $dateTime;

    /**
     * @param Action\Context $context
     * @param NotificationFactory $notificationFactory
     * @param Notification $notification
     * @param Json $serialize
     * @param Helper $helper
     * @param DateTime $dateTime
     */
    public function __construct(
        Action\Context      $context,
        NotificationFactory $notificationFactory,
        Notification        $notification,
        Json                $serialize,
        Helper              $helper,
        DateTime            $dateTime
    )
    {
        parent::__construct($context);
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->notification = $notification;
        $this->notificationFactory = $notificationFactory;
        $this->serialize = $serialize;
        $this->helper = $helper;
        $this->dateTime = $dateTime;
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $now = $this->dateTime->gmtDate();

            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $data = $this->getRequest()->getPostValue();

            if (isset($data['total_click'])) {
                unset($data['total_click']);
            }
            if (isset($data['total_sent'])) {
                unset($data['total_sent']);
            }
            if (isset($data['update_at'])) {
                unset($data['update_at']);
            }
            if (isset($data['created_at'])) {
                unset($data['created_at']);
            }

            if (isset($data['notification_type'])) {
                if (
                    (
                        $data['notification_type'] == NotificationModel::REVIEW_REMINDERS ||
                        $data['notification_type'] == NotificationModel::ORDER_STATUS_UPDATE ||
                        $data['notification_type'] == NotificationModel::BIRTHDAY
                    ) && $data['send_time'] == 'schedule_time'
                ) {
                    $data['send_time'] = 'send_immediately';
                    $this->messageManager->addWarningMessage('This notification type cannot be scheduled, it will be switched to the sending immediately mode');
                }
                if (
                    $data['notification_type'] == NotificationModel::ABANDONED_CART_REMINDS &&
                    (
                        $data['send_time'] == 'schedule_time' ||
                        $data['send_time'] == 'send_after_the_trigger_condition'
                    )
                ) {
                    $data['send_time'] = 'send_immediately';
                    $this->messageManager->addWarningMessage('Cannot schedule or add conditions to this notification type, it will be switched to the sending immediately mode');
                }
            }


            if (isset($data['schedule_to']) && isset($data['send_time']) && $data['send_time'] == 'schedule_time') {
                $scheduleTo = date("Y-m-d H:i:s", strtotime($data['schedule_to']));
                if ($scheduleTo <= $now) {
                    $this->messageManager->addWarningMessage('Schedule must not be earlier than current time');
                    if (isset($data['id'])) {
                        return $resultRedirect->setPath('*/*/newAction/id/' . $data['id']);
                    } else {
                        return $resultRedirect->setPath('*/*/newAction');
                    }
                }
            }

            if ($data) {
                $id = $this->getRequest()->getParam('id');
                $model = $this->notificationFactory->create();
                $this->notification->load($model, $id);
                if (isset($data['store_view'])) {
                    $data['store_view'] = $this->serialize->serialize($data['store_view']);
                }

                if (isset($data['customer_group'])) {
                    $data['customer_group'] = $this->serialize->serialize($data['customer_group']);
                }

                if ($data['notification_type'] == NotificationModel::REVIEW_REMINDERS) {
                    $data['condition'] = $this->serialize->serialize($data['order_status_review']);
                } elseif ($data['notification_type'] == NotificationModel::ORDER_STATUS_UPDATE) {
                    $data['condition'] = $this->serialize->serialize($data['order_status']);
                } elseif ($data['notification_type'] == NotificationModel::ABANDONED_CART_REMINDS) {
                    $data['condition'] = $data['set_abandoned_cart_time'];
                } elseif ($data['notification_type'] == NotificationModel::BIRTHDAY) {
                    $data['condition'] = $data['set_notification_sent_before'];
                } elseif ($data['notification_type'] == NotificationModel::CUSTOMER_LOGIN) {
                    $data['condition'] = $data['set_time_after_login'];
                } elseif ($data['notification_type'] == NotificationModel::MAINTENANCE) {
                    $data['condition'] = $data['set_time_after_buy'];
                } elseif ($data['notification_type'] == NotificationModel::CUSTOM_TYPE) {
                    $data['condition'] = $data['custom_notification_type'];
                }

                if (isset($data['send_time'])) {
                    if ($data['send_time'] == "schedule_time") {
                        $data['schedule'] = $data['schedule_to'];
                    }
                    if ($data['send_time'] == "send_after_the_trigger_condition") {
                        $chedule = ['send_after' => $data['send_after'], 'unit' => $data['unit']];
                        $data['schedule'] = $this->serialize->serialize($chedule);
                    }
                }
                unset($data['total_click']);
                unset($data['impression']);
                $data['is_sent'] = NotificationModel::IS_NOT_SENT;
                $model->addData($data);
                $this->notification->save($model);
                $data['id'] = $model->getId();

                // check if type != default and send notification immediately
                if (
                    $data['send_time'] == 'send_immediately' &&
                    !in_array($data['notification_type'], self::LIST_NOTIFICATION_TYPE) &&
                    $data['is_active']
                ) {
                    $this->messageManager->addSuccessMessage(__('The Notification is added to queue. Make sure your cron job is running to send notification'));
                } else {
                    $this->messageManager->addSuccessMessage(__('The Notification has been saved.'));
                }

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/newAction/id/' . $model->getId());
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }

        return $resultRedirect->setPath('*/*/');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_NotificationBox::notification');
    }
}
