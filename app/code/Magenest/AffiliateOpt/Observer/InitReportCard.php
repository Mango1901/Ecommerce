<?php

namespace Magenest\AffiliateOpt\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class InitReportCard
 * @package Magenest\AffiliateOpt\Observer
 */
class InitReportCard implements ObserverInterface
{
    const MP_AFFILIATE_DASHBOARD = 'Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard';
    const MP_AFFILIATE_TRANSACTION = self::MP_AFFILIATE_DASHBOARD . '\Transaction';
    const MP_AFFILIATE_NEW_ACCOUNT = self::MP_AFFILIATE_DASHBOARD . '\NewAccount';
    const MP_AFFILIATE_TOP_ACCOUNT = self::MP_AFFILIATE_DASHBOARD . '\TopAccount';
    const MP_AFFILIATE_BESTSELLERS = self::MP_AFFILIATE_DASHBOARD . '\Bestsellers';

    /**
     * @param EventObserver $observer
     */
    public function execute(EventObserver $observer)
    {
        $carts = $observer->getEvent()->getCards();
        $carts->setMpAffiliateTransaction(self::MP_AFFILIATE_TRANSACTION)
            ->setNewAccount(self::MP_AFFILIATE_NEW_ACCOUNT)
            ->setTopAccount(self::MP_AFFILIATE_TOP_ACCOUNT)
            ->setBestsellers(self::MP_AFFILIATE_BESTSELLERS);//
    }
}
