<?php

namespace Magenest\AffiliateOpt\Controller\Adminhtml\Reports;

use Magento\Framework\Phrase;
use Magenest\AffiliateOpt\Controller\Adminhtml\AbstractAction;

/**
 * Class Sales
 * @package Magenest\AffiliateOpt\Controller\Adminhtml\Reports
 */
class Sales extends AbstractAction
{
    /**
     * @return Phrase
     */
    public function getPageTitle()
    {
        return __('Sales Reports');
    }
}
