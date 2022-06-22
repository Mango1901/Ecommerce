<?php
namespace Magenest\NotificationBox\Controller\HandleNotification;

use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\NotificationFactory;
use Magenest\NotificationBox\Model\ResourceModel\Notification;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Psr\Log\LoggerInterface;

class ClickToNotification extends Action
{
    /** @var Helper  */
    protected $helper;

    /** @var NotificationFactory  */
    protected $notificationFactory;

    /** @var Notification  */
    protected $notificationResource;

    /** @var LoggerInterface  */
    protected $loggerInterface;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param NotificationFactory $notificationFactory
     * @param Notification $notificationResource
     * @param LoggerInterface $loggerInterface
     */
    public function __construct(
        Context $context,
        Helper $helper,
        NotificationFactory $notificationFactory,
        Notification $notificationResource,
        LoggerInterface $loggerInterface
    ) {
        $this->loggerInterface              = $loggerInterface;
        $this->notificationResource         = $notificationResource;
        $this->notificationFactory          = $notificationFactory;
        $this->helper                       = $helper;
        parent::__construct($context);
    }

    /** update field notification */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();
        if (isset($params['impression'])) {
            $type = 'UpdateImpression';
            $notificationId = $params['impression'];
        }
        if (isset($params['notificationId'])) {
            $type = 'updateTotalClick';
            $notificationId = $params['notificationId'];
        }
        $redirectUrl = ($params['url'] !== null && strlen(trim($params['url']))) ? $params['url'] : null;
        if (isset($notificationId)) {
            $this->updateData($type, $notificationId);
        }
        if (isset($redirectUrl)) {
            $resultRedirect->setUrl($params['url']);
        } else {
            $resultRedirect->setPath("");
        }
        return $resultRedirect;
    }

    /** update field notification
     * @param $type
     * @param $notificationId
     */
    private function updateData($type, $notificationId)
    {
        try {
            $notificationModel = $this->notificationFactory->create();
            $this->notificationResource->load($notificationModel, $notificationId);
            if ($type == 'UpdateImpression') {
                $notificationModel->setData('impression', $notificationModel->getImpression()+1);
            } elseif ($type == 'updateTotalClick') {
                $notificationModel->setData('total_click', $notificationModel->getTotalClick()+1);
            }
            $this->notificationResource->save($notificationModel);
        } catch (\Exception $exception) {
            $this->loggerInterface->error('update fail: ' . $exception->getMessage());
        }
    }
}
