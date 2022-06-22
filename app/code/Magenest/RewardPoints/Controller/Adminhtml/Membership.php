<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 29/10/2020 14:51
 */

namespace Magenest\RewardPoints\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

abstract class Membership extends Action
{
    /**
     * @var PageFactory
     */
    protected $_pageFactory;

    /**
     * Membership constructor.
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        PageFactory $pageFactory,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_pageFactory->create();
        $resultPage->setActiveMenu('Magenest_RewardPoints::system_rewardpoints_membership')
            ->addBreadcrumb(__('Membership Group'), __('Membership Group Manager'));

        $resultPage->getConfig()->getTitle()->set(__('Membership Group Manager'));

        return $resultPage;
    }

    /**
     * @inheritDoc
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_RewardPoints::system_rewardpoints_membership');
    }
}