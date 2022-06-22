<?php

namespace Magenest\Affiliate\Block\Account\Home;

use Magento\Framework\Exception\LocalizedException;
use Magenest\Affiliate\Block\Account\Home;

/**
 * Class Transaction
 * @package Magenest\Affiliate\Block\Account\Home
 */
class Transaction extends Home
{
    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function getTransactions()
    {
        $format = 'Y-m-d';

        $currentDate = $this->_localeDate->date()->format($format);

        $startDate = $this->_request->getParam('from_date') ??
            date('Y-m-d', strtotime($currentDate. ' - 30 days'));
        $endDateParam = $this->_request->getParam('to_date');
        $endDate = $endDateParam ?
            date('Y-m-d', strtotime($endDateParam. ' +1 days')) :
            date('Y-m-d', strtotime($currentDate. ' +1 days'));

        $collection = $this->transactionFactory->create()
            ->getCollection()
            ->addFieldToFilter('account_id', $this->getCurrentAccount()->getId())
            ->addFieldToFilter('created_at', ["gteq" => $startDate])
            ->addFieldToFilter('created_at', ["lteq" => $endDate]);

        $collection->setOrder('transaction_id', 'DESC');

        if ($collection->getSize()) {
            // create pager block for collection
            $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'affiliate.transaction.pager');
            // assign collection to pager
            $limit = $this->_request->getParam('limit') ?: 10;
            $pager->setLimit($limit)->setCollection($collection);
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

    /**
     * get customer email by order id
     *
     * @param $orderId
     *
     * @return string
     */
    public function getCustomerEmailByOrderId($orderId)
    {
        return $this->_affiliateHelper->getCustomerEmailByOId($orderId);
    }
}
