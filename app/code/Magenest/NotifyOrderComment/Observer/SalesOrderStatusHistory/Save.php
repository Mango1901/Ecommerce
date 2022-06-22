<?php


namespace Magenest\NotifyOrderComment\Observer\SalesOrderStatusHistory;


use Magenest\NotifyOrderComment\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class Save
 * @package Magenest\NotifyOrderComment\Observer\SalesOrderStatusHistory
 */
class Save implements ObserverInterface
{
    public $request;
    public $dataHlp;

    /**
     * Save constructor.
     * @param Data $dataHlp
     * @param RequestInterface $request
     */
    public function __construct(
        Data $dataHlp,
        RequestInterface $request
    )
    {
        $this->dataHlp = $dataHlp;
        $this->request = $request;

    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $history = $this->request->getParam('history');
        $statusHistory = $observer->getStatusHistory();
        if($history['history_notify_to_admin'] ?? ""){
            $emailArr = explode(',',$history['history_notify_email_admin']);
            $emailFirst = $emailArr[0] ?? "";
            unset($emailArr[0]);
            if ($emailFirst) {
                $this->dataHlp->sendEmailToAdmin($emailFirst,$emailArr,$statusHistory);
            }
        }
    }

}
