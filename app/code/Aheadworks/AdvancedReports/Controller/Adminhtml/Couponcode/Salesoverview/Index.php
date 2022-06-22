<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\AdvancedReports\Controller\Adminhtml\Couponcode\Salesoverview;

class Index extends \Aheadworks\AdvancedReports\Controller\Adminhtml\Couponcode\Salesoverview
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $couponCode = $this->_request->getParam('coupon_code', null);
        if (null === $couponCode) {
            $redirectResult = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            return $redirectResult->setUrl($this->_redirect->getRefererUrl());
        }
        $resultPage = $this->_initAction();
        return $resultPage;
    }
}
