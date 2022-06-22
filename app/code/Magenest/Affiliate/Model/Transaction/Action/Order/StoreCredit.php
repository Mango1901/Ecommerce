<?php


namespace Magenest\Affiliate\Model\Transaction\Action\Order;

use Magento\Framework\Phrase;
use Magento\Sales\Model\Order;
use Magenest\Affiliate\Model\Transaction\AbstractAction;
use Magenest\Affiliate\Model\Transaction\Status;
use Magenest\Affiliate\Model\Transaction\Type;

/**
 * Class StoreCredit
 */
class StoreCredit extends AbstractAction
{
    /**
     * @return int
     */
    public function getAmount()
    {
        return -(float)$this->getObject()->getAmount();
    }

    /**
     * @return int
     */
    public function getType()
    {
        return Type::PAID;
    }

    /**
     * @param null $transaction
     *
     * @return Phrase
     */
    public function getTitle($transaction = null)
    {
        return __('Subtract balance for store credit');
    }
}
