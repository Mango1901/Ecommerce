<?php
namespace Magenest\Affiliate\Block\Adminhtml\Transaction;

use Magento\Backend\Block\Template;

class Balance extends Template
{
    public function getAccountBalance()
    {
        $balance = $this->_cache->load('affiliate_balance');

        if (!$balance) {
            return "Chưa cập nhật số dư";
        }

        return $balance;
    }
}
