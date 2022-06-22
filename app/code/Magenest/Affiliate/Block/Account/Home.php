<?php


namespace Magenest\Affiliate\Block\Account;

use Magenest\Affiliate\Block\Account;

/**
 * Class Home
 * @package Magenest\Affiliate\Block\Account
 */
class Home extends Account
{
    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('My Credit'));

        return parent::_prepareLayout();
    }

    /**
     * get account balance
     *
     * @return mixed
     */
    public function getAccountBalance()
    {
        return $this->getCurrentAccount()->getBalance();
    }

    /**
     * get account holding balance
     *
     * @return mixed
     */
    public function getAccountHoldingBalance()
    {
        return $this->getCurrentAccount()->getHoldingBalance();
    }

    /**
     * get account Total Commission
     *
     * @return mixed
     */
    public function getAccountTotalCommission()
    {
        return $this->getCurrentAccount()->getTotalCommission();
    }

    /**
     * get account Total Paid
     *
     * @return mixed
     */
    public function getAccountTotalPaid()
    {
        return $this->getCurrentAccount()->getTotalPaid();
    }
}
