<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_SmartNet extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_SmartNet
 */

namespace Magenest\MegaMenu\Block\Adminhtml\Menu\Edit;

class SwitchPopup extends \Magento\Backend\Block\Template
{
    protected $_template = 'Magenest_MegaMenu::menu/version-popup.phtml';

    protected $logCollection;

    /**
     * SwitchPopup constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magenest\MegaMenu\Model\ResourceModel\MenuLog\CollectionFactory $logCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magenest\MegaMenu\Model\ResourceModel\MenuLog\CollectionFactory $logCollection,
        array $data = []
    ) {
        $this->logCollection = $logCollection;
        parent::__construct($context, $data);
    }

    public function getMenuLogList()
    {
        $result = [];
        $menuId = (int)$this->getRequest()->getParam('id');
        $menuLogCol = $this->logCollection->create()->addCurrentVersionFilter($menuId);
        $menuLogCol->addFieldToFilter('menu_id', $menuId);
        foreach ($menuLogCol as $item) {
            $result[$item->getVersion()] = __("Version %1. %2", $item->getVersion(), $item->getNote());
        }

        return $result;
    }
}
