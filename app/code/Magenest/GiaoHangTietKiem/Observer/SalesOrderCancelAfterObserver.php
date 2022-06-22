<?php

namespace Magenest\GiaoHangTietKiem\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class SalesOrderCancelAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var GiaoHangTietKiem
     */
    protected $ghtkCarrier;

    /**
     * SalesOrderCancelAfterObserver constructor.
     * @param GiaoHangTietKiem $ghtkCarrier
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        GiaoHangTietKiem $ghtkCarrier,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->ghtkCarrier = $ghtkCarrier;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param Observer $observer
     * @throws \Zend_Http_Client_Exception
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order) {
            $order = $observer->getEvent()->getPayment()->getOrder();
        }
        if ($order && $order->getApiOrderId()) {
            if ($order->getShippingMethod() == 'giaohangtietkiem_giaohangtietkiem') {
                $ghtkLabelId = $order->getApiOrderId();
                $params    = [
                    'token' => $this->scopeConfig->getValue("carriers/giaohangtietkiem/api_token"),
                ];
                $this->ghtkCarrier->cancelOrder($params, $ghtkLabelId);
            }

        }
    }
}
