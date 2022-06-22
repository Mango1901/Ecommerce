<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Controller\Adminhtml;

use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magenest\MegaMenu\Model\MegaMenuFactory;
use Magento\Framework\View\Result\PageFactory;
use Magenest\MegaMenu\Model\MenuEntityFactory;

/**
 * Class Menu
 * @package Magenest\MegaMenu\Controller\Adminhtml
 */
abstract class Menu extends \Magento\Backend\App\Action
{
    /** @var MegaMenuFactory */
    protected $_megaMenuFactory;

    /** @var MenuEntityFactory */
    protected $_menuEntityFactory;

    /** @var PageFactory */
    protected $_pageFactory;

    /** @var Registry */
    protected $_coreRegistry;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param PageFactory $pageFactory
     * @param MegaMenuFactory $megaMenuFactory
     * @param MenuEntityFactory $menuEntityFactory
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        MegaMenuFactory $megaMenuFactory,
        MenuEntityFactory $menuEntityFactory,
        Registry $registry
    ) {
        $this->_megaMenuFactory = $megaMenuFactory;
        $this->_menuEntityFactory = $menuEntityFactory;
        $this->_coreRegistry = $registry;
        $this->_pageFactory = $pageFactory;
        parent::__construct($context);
    }

    /**
     * Init Action.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magenest_MegaMenu::menu')
            ->addBreadcrumb(__('MegaMenu Gallery Date'), __('MegaMenu Gallery Date'));

        $resultPage->getConfig()->getTitle()->set(__('MegaMenu Gallery Date'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_MegaMenu::menu');
    }
}
