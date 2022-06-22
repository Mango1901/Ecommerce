<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

/**
 * Menu
 *
 * @method Menu setTitle(string $title)
 * @method string getTitle()
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 * @codeCoverageIgnore
 */
class Menu extends \Magento\Backend\Block\Template
{
    /**
     * Block template filename
     *
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/menu.phtml';

    /**
     * Retrieve first crumb
     *
     * @return string
     */
    public function getFirstCrumb()
    {
        /** @var \Aheadworks\AdvancedReports\Block\Adminhtml\View */
        return $this->getParentBlock()->getBreadcrumbs()->getFirstLastCrumb();
    }
}
