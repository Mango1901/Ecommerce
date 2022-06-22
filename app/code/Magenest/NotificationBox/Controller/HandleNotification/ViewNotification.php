<?php
namespace Magenest\NotificationBox\Controller\HandleNotification;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magenest\NotificationBox\Model\ResourceModel\CustomerNotification;
use Magenest\NotificationBox\Model\CustomerNotificationFactory;
use Magenest\NotificationBox\Model\CustomerNotification as CustomerNotificationModel;

class ViewNotification extends Action
{
    /** @var PageFactory  */
    protected $resultPageFactory;

    /** @var CustomerNotificationFactory  */
    protected $customerNotificationFactory;

    /** @var CustomerNotification  */
    protected $customerNotificationResource;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param CustomerNotificationFactory $customerNotificationFactory
     * @param CustomerNotification $customerNotificationResource
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        CustomerNotificationFactory $customerNotificationFactory,
        CustomerNotification $customerNotificationResource
    ) {
        parent::__construct($context);
        $this->customerNotificationFactory  = $customerNotificationFactory;
        $this->customerNotificationResource = $customerNotificationResource;
        $this->resultPageFactory            = $resultPageFactory;
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();
        $notificationId = $params['id'];
        $notificationModel = $this->customerNotificationFactory->create();
        $this->customerNotificationResource->load($notificationModel,$notificationId);
        $url = $notificationModel['redirect_url'] ?? $this->_redirect->getRefererUrl();
        if(!$notificationModel->getStatus()){
            $notificationModel->setData('status',CustomerNotificationModel::STATUS_READ);
            $this->customerNotificationResource->save($notificationModel);
        }

        $resultRedirect->setUrl($url);
        return $resultRedirect;
    }
}
