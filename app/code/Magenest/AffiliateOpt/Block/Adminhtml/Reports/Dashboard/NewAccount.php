<?php

namespace Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard;

use Magento\Framework\Phrase;

/**
 * Class AffiliateOpt
 * @package Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard
 */
class NewAccount extends Reports
{
    const MAGE_REPORT_CLASS = NewAccount::class;

    /**
     * @var string
     */
    protected $_template = 'Magenest_AffiliateOpt::reports/dashboard/new_account.phtml';

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('New Affiliate');
    }

    /**
     * @return mixed
     */
    public function getNewAccount()
    {
        $date = $this->getReportsHelper()->getDateRange();
        $accountCollection = $this->affiliateAccount->create()
            ->getCollection()
            ->addFieldToFilter('main_table.created_at', ['gteq' => $date[0]])
            ->addFieldToFilter('main_table.created_at', ['lteq' => $date[1]])
            ->setOrder('created_at', 'DESC');
        $accountCollection->getSelect()->join(
            ['customer' => $accountCollection->getTable('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            ['email', 'firstname', 'lastname']
        );
        $accountCollection->setPageSize(5);

        return $accountCollection;
    }
}
