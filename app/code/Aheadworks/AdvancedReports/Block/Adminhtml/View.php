<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Flag;

/**
 * Class View
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml
 */
class View extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view.phtml';

    /**
     * @var Flag
     */
    private $flag;

    /**
     * View constructor.
     * @param Context $context
     * @param Flag $flag
     * @param array $data
     */
    public function __construct(
        Context $context,
        Flag $flag,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->flag = $flag;
    }

    /**
     * Show the date of last update reports index
     *
     * @return $this
     */
    public function showLastIndexUpdate()
    {
        $updatedAt = 'undefined';
        $flag = $this->flag->setReportFlagCode(Flag::AW_AREP_STATISTICS_FLAG_CODE)->loadSelf();
        if ($flag->hasData()) {
            $updatedAt =  $this->_localeDate->formatDate(
                $flag->getLastUpdate(),
                \IntlDateFormatter::MEDIUM,
                true
            );
        }

        return __('The latest Advanced Reports index was updated on %1', $updatedAt);
    }

    /**
     * Retrieve breadcrumbs block
     *
     * @return \Aheadworks\AdvancedReports\Block\Adminhtml\View\Breadcrumbs
     */
    public function getBreadcrumbs()
    {
        return $this->getChildBlock('breadcrumbs');
    }
}
