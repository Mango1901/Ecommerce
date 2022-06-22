<?php


namespace Magenest\Affiliate\Block\Account\Withdraw;

use Magenest\Affiliate\Block\Account\Withdraw;

/**
 * Class View
 * @package Magenest\Affiliate\Block\Account\Withdraw
 */
class View extends Withdraw
{
    /**
     * @return mixed
     */
    public function getWithdraw()
    {
        return $this->registry->registry('withdraw_view_data');
    }

    /**
     * @return mixed
     */
    public function getPaymentDetail()
    {
        $withdraw = $this->getWithdraw();

        return $withdraw->getPaymentModel()->getPaymentDetail();
    }
}
