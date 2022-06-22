<?php

namespace Magenest\AffiliateOpt\Controller\Adminhtml\Reports;

use Magento\Framework\Phrase;
use Magenest\AffiliateOpt\Controller\Adminhtml\AbstractAction;

/**
 * Class Bestsellers
 * @package Magenest\AffiliateOpt\Controller\Adminhtml\Reports
 */
class Bestsellers extends AbstractAction
{
    /**
     * @return Phrase
     */
    public function getPageTitle()
    {
        return __('Bestsellers');
    }
}
