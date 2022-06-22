<?php


namespace Magenest\Affiliate\Model\Transaction\Action\Withdraw;

use Magento\Framework\Phrase;
use Magenest\Affiliate\Model\Transaction\AbstractAction;
use Magenest\Affiliate\Model\Transaction\Type;

/**
 * Class Create
 * @package Magenest\Affiliate\Model\Transaction\Action\Withdraw
 */
class Create extends AbstractAction
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
        return __('Subtract balance for withdraw request');
    }
}
