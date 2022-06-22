<?php

namespace Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard;

use Magento\Framework\Phrase;
use Magenest\Affiliate\Model\Transaction\Type;
use Magenest\AffiliateOpt\Model\ResourceModel\Order\Collection;

/**
 * Class AffiliateOpt
 * @package Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard
 */
class TopAccount extends Reports
{
    const MAGE_REPORT_CLASS = TopAccount::class;

    /**
     * @var string
     */
    protected $topAffiliate = '';

    /**
     * @var string
     */
    protected $_template = 'Magenest_AffiliateOpt::reports/dashboard/top_account.phtml';

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Top Affiliate');
    }

    /**
     * @return Collection
     */
    public function getNewAccount()
    {
        if (!$this->topAffiliate) {
            $affiliateAccount = $this->affiliateAccount->create()->getCollection();
            $customerCollection = $this->customerFactory->create()->getCollection()->addNameToSelect();
            $transactionCollection = $this->collection->create()->addFieldToFilter('type', Type::COMMISSION);
            $date = $this->getReportsHelper()->getDateRange();
            $this->topAffiliate = $this->orderCollection->jointSelectSql(
                $affiliateAccount,
                $customerCollection,
                $transactionCollection,
                $date
            );
        }

        return $this->topAffiliate;
    }
}
