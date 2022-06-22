<?php

namespace Magenest\CustomSource\Observer;

use Magenest\CustomSource\Plugin\SetAreaCodeContext;
use Magenest\CustomSource\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesConvertQuote
 *
 * @package Magenest\OrderExtraInformation\Observer
 */
class SalesConvertQuote implements ObserverInterface
{
    protected $data;

    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $areaCode = $quote->getAreaCode();
        if (!$areaCode) {
            $areaCode = $this->data->getCurrentArea();
        }
        $order->setAreaCode($areaCode);

        return $this;
    }
}
