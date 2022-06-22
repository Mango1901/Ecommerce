<?php


namespace Magenest\Affiliate\Model\Transaction\Action\Order;

use Magento\Framework\Phrase;
use Magento\Sales\Model\Order;
use Magenest\Affiliate\Model\Transaction\AbstractAction;
use Magenest\Affiliate\Model\Transaction\Status;
use Magenest\Affiliate\Model\Transaction\Type;

/**
 * Class Invoice
 *
 * @package Magenest\Affiliate\Model\Transaction\Action\Order
 */
class Invoice extends AbstractAction
{
    /**
     * @return mixed
     */
    public function getAmount()
    {
        $object = $this->getObject();
        $amount = $object->getCommissionAmount();

        if ($object instanceof Order) {
            $amount -= $this->transactionFactory->create()
                ->getCollection()
                ->addFieldToFilter('account_id', $this->getAccount()->getId())
                ->addFieldToFilter('action', 'order/invoice')
                ->addFieldToFilter('order_id', $object->getId())
                ->getFieldTotal();
        }

        return max(0, $amount);
    }

    /**
     * @return int
     */
    public function getType()
    {
        return Type::COMMISSION;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        $holdDays = $this->getHoldDays();
        if ($holdDays && $holdDays > 0) {
            return Status::STATUS_HOLD;
        }

        return Status::STATUS_COMPLETED;
    }

    /**
     * @param null $transaction
     *
     * @return Phrase
     */
    public function getTitle($transaction = null)
    {
        $param = $transaction === null
            ? '#' . $this->getOrder()->getIncrementId()
            : '#' . $transaction->getOrderIncrementId();

        return __('Nhận hoa hồng từ đơn đặt hàng %1', $param);
    }

    /**
     * @return array
     */
    public function prepareAction()
    {
        $order           = $this->getOrder();
        $transactionData = [
            'order_id'           => $order->getId(),
            'order_increment_id' => $order->getIncrementId(),
            'store_id'           => $order->getStoreId(),
            'campaign_id'        => $order->getAffiliateCampaigns(),
            'total_commission'   => $order->getTotalCommission(),
            'tax_deduction'      => $order->getTaxDeduction(),
        ];

        $holdDays = $this->getHoldDays();
        if ($holdDays > 0) {
            $transactionData['holding_to'] = $this->getHoldingDate($holdDays);
        }

        return $transactionData;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        $object = $this->getObject();
        if ($object instanceof Order\Invoice) {
            $order = $object->getOrder();
        } else {
            $order = $object;
        }

        return $order;
    }

    /**
     * @return string
     */
    public function getAdditionContent()
    {
        $extraContent = $this->getExtraContent();
        $object       = $this->getObject();
        if ($object instanceof Order\Invoice) {
            $extraContent['invoice_increment_id'] = $object->getIncrementId();
        }

        return $this->jsonHelper->jsonEncode($extraContent);
    }
}
