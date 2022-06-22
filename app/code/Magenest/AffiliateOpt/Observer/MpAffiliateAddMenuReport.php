<?php

namespace Magenest\AffiliateOpt\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\AffiliateOpt\Helper\Data as HelperData;

/**
 * Class MpAffiliateAddMenuReport
 * @package Magenest\AffiliateOpt\Observer
 */
class MpAffiliateAddMenuReport implements ObserverInterface
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * Index constructor.
     *
     * @param HelperData $helperData
     */
    public function __construct(HelperData $helperData)
    {
        $this->helperData = $helperData;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->helperData->canUseStoreSwitcherLayoutByMpReports()) {
            $resultPage = $observer->getEvent()->getResultPage();
            $resultPage->addHandle('store_switcher');
            $resultPage->addHandle('transaction_block');
        }
    }
}
