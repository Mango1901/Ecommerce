<?php


namespace Magenest\Affiliate\Model\Transaction\Action;

use Magento\Framework\Phrase;
use Magenest\Affiliate\Model\Transaction\AbstractAction;
use Magenest\Affiliate\Model\Transaction\Status;
use Magenest\Affiliate\Model\Transaction\Type;

/**
 * Class Admin
 * @package Magenest\Affiliate\Model\Transaction\Action
 */
class Admin extends AbstractAction
{
    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->dataHelper->getPriceCurrency()->round($this->getObject()->getAmount());
    }

    /**
     * @return int
     */
    public function getType()
    {
        return Type::ADMIN;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        $holdDays = $this->getObject()->getHoldDay();
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
        $object = $transaction === null ? $this->getObject() : $transaction;

        return $object->getTitle() ?: __('Changed by Admin');
    }

    /**
     * @return array
     */
    public function prepareAction()
    {
        $actionObject = $this->getObject();
        if ($holdDay = $actionObject->getHoldDay()) {
            return ['holding_to' => $this->getHoldingDate($holdDay)];
        }

        return [];
    }
}
