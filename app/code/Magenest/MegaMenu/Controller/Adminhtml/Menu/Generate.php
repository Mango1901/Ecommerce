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

namespace Magenest\MegaMenu\Controller\Adminhtml\Menu;

class Generate extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Magenest_MegaMenu::manage';
    protected $_generator;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magenest\MegaMenu\Helper\MenuGenerator $generator
    ) {
        parent::__construct($context);
        $this->_generator = $generator;
    }

    public function execute()
    {
        $result = $this->_generator->generateGridMenuByCategories();
        if ($result === true) {
            $this->messageManager->addSuccessMessage(__("Generate menu successfully."));
        } else {
            $this->messageManager->addErrorMessage($result);
        }

        return $this->_redirect('*/*/');
    }
}
