<?php
namespace Magenest\NotificationBox\Controller\HandleNotification;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Asset\Repository;
use Magenest\NotificationBox\Helper\Helper;
use Magento\Store\Model\StoreManagerInterface;

class HandleConfirmBox extends Action
{

    /** @var JsonFactory  */
    protected  $resultJsonFactory;

    /** @var Repository  */
    protected $repository;

    /** @var Helper  */
    protected $helper;

    /** @var StoreManagerInterface  */
    protected $storeManagerInterface;


    /**
     * @param StoreManagerInterface $storeManagerInterface
     * @param Repository $repository
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param Helper $helper
     */
    public function __construct(
        StoreManagerInterface $storeManagerInterface,
        Repository $repository,
        Context $context,
        JsonFactory $resultJsonFactory,
        Helper $helper
    )
    {
        $this->storeManagerInterface    = $storeManagerInterface;
        $this->helper                   = $helper;
        $this->repository               = $repository;
        $this->resultJsonFactory        = $resultJsonFactory;
        parent::__construct($context);
    }

    /** save customer Token */
    public function execute()
    {
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        try {
            $time           = $this->helper->getTimeResendPopUp();
            $timeShowPopup  = $this->helper->getTimeShowPopup();
            $senderId       = $this->helper->getSenderId();
            $askAllowSubscription = $this->helper->getAllowCustomerDeleteNotification();
            $contentPopup = $this->helper->getContentPopup();
            $askCustomersToAllowWebPushSubscriptions = $this->helper->getAllowWebPush();
            $urlFirebase = $this->storeManagerInterface->getStore()->getBaseUrl().'pub/firebase-messaging-sw.js';

            $data = [
                'time'=>$time*1000,
                'timeShowPopup'=>$timeShowPopup*1000,
                'senderId' =>$senderId,
                'askAllowSubscription' =>$askAllowSubscription,
                'contentPopup' => $contentPopup,
                'askCustomersToAllowWebPushSubscriptions' => $askCustomersToAllowWebPushSubscriptions,
                'urlFirebase'=>$urlFirebase
            ];
            return $result->setData($data);
        }
        catch (\Exception $exception){
            return $result->setData("fail");
        }
    }
}
