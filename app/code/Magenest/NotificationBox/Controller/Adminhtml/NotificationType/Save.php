<?php
namespace Magenest\NotificationBox\Controller\Adminhtml\NotificationType;

use Magenest\NotificationBox\Model\NotificationTypeFactory;
use Magenest\NotificationBox\Model\ResourceModel\NotificationType;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Save
 * @package Magenest\InstagramShop\Controller\Adminhtml\Hotspot
 */
class Save extends \Magento\Backend\App\Action
{
    /** @var NotificationType  */
    protected $notificationType;

    /** @var NotificationTypeFactory  */
    protected $notificationTypeFactory;

    /** @var Json  */
    protected $serialize;

    /**
     * @param Action\Context $context
     * @param NotificationTypeFactory $notificationTypeFactory
     * @param NotificationType $notificationType
     * @param Json $serialize
     */
    public function __construct(
        Action\Context $context,
        NotificationTypeFactory $notificationTypeFactory,
        NotificationType $notificationType,
        Json $serialize
    )
    {
        parent::__construct($context);
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->notificationType= $notificationType;
        $this->notificationTypeFactory = $notificationTypeFactory;
        $this->serialize = $serialize;
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $data           = $this->getRequest()->getPostValue();
            if ($data) {
                $id = $this->getRequest()->getParam('entity_id');
                $model = $this->notificationTypeFactory->create();
                $this->notificationType->load($model, $id);
                if (isset($data['icon'])) {
                    $data['icon'] = $this->serialize->serialize($data['icon']);
                    //$model->setData($data);
                }
                unset($data['form_key']);
                unset($data['entity_id']);
                $model->addData($data);
                $this->notificationType->save($model);
                $this->messageManager->addSuccessMessage(__('The Notification Type has been saved.'));
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/newAction/entity_id/' . $model->getId());
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        return $resultRedirect->setPath('*/*/');
    }
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_NotificationBox::notification_type');
    }
}
