<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MegaMenu\Block\Adminhtml\Menu\Edit;

use Magento\Backend\Block\Widget\Tabs as CoreTabs;

/**
 * Class Tabs
 * @package Magenest\MegaMenu\Block\Adminhtml\Menu\Edit
 */
class Tabs extends CoreTabs
{
    /**
     * @inheritdoc
     */
    protected $_template = 'Magento_Backend::widget/tabshoriz.phtml';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('template_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Menu Configuration'));
    }
}
