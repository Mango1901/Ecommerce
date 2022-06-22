<?php
namespace Magenest\PaymentEPay\Model\Order\Email\Sender;

use Magento\Sales\Model\Order;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;

class OrderSender extends \Magento\Sales\Model\Order\Email\Sender\OrderSender
{

    public function send(Order $order, $forceSyncMode = false)
    {
        $payment = $order->getPayment()->getMethodInstance()->getCode();

        if($payment == PaymentAttributeInterface::CODE_VNPT_EPAY && $order->getStatus() != 'pending'){
            return false;
        }

        $this->identityContainer->setStore($order->getStore());
        $order->setSendEmail($this->identityContainer->isEnabled());

        if (!$this->globalConfig->getValue('sales_email/general/async_sending') || $forceSyncMode) {
            if ($this->checkAndSend($order)) {
                $order->setEmailSent(true);
                $this->orderResource->saveAttribute($order, ['send_email', 'email_sent']);
                return true;
            }
        } else {
            $order->setEmailSent(null);
            $this->orderResource->saveAttribute($order, 'email_sent');
        }

        $this->orderResource->saveAttribute($order, 'send_email');

        return false;
    }
}
