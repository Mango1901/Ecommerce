<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 02/11/2020 16:25
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\PageFactory;

class NewAction extends Membership
{
    /**
     * @var ForwardFactory
     */
    protected $_resultForwardFactory;

    /**
     * NewAction constructor.
     * @param ForwardFactory $forwardFactory
     * @param PageFactory $pageFactory
     * @param Action\Context $context
     */
    public function __construct(
        ForwardFactory $forwardFactory,
        PageFactory $pageFactory,
        Action\Context $context
    ) {
        parent::__construct($pageFactory, $context);
        $this->_resultForwardFactory = $forwardFactory;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}