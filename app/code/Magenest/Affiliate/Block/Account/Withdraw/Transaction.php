<?php


namespace Magenest\Affiliate\Block\Account\Withdraw;

use Magento\Framework\Exception\LocalizedException;
use Magenest\Affiliate\Block\Account\Withdraw;

/**
 * Class Transaction
 * @package Magenest\Affiliate\Block\Account\Withdraw
 */
class Transaction extends Withdraw
{
    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getWithdraws()
    {
        $collection = $this->withdrawFactory->create()
            ->getCollection()
            ->addFieldToFilter('account_id', $this->getCurrentAccount()->getId());

        $collection->setOrder('withdraw_id', 'DESC');

        if ($collection->getSize()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'affiliate.transaction.pager');
            // assign collection to pager
            $pager->setLimit(10)->setCollection($collection);
            $this->setChild('pager', $pager);// set pager block in layout
        }

        return $collection;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
