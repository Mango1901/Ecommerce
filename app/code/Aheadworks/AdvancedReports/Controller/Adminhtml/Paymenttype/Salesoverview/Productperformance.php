<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\AdvancedReports\Controller\Adminhtml\Paymenttype\Salesoverview;

class Productperformance extends \Aheadworks\AdvancedReports\Controller\Adminhtml\Paymenttype\Salesoverview
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
        $redirectResult = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $paymentType = $this->_request->getParam('payment_type', null);
        if (null === $paymentType) {
            $redirectResult = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            return $redirectResult->setUrl($this->_redirect->getRefererUrl());
        }
        $redirectResult->setUrl($this->getUrl('*/paymenttype_productperformance/index', ['_current' => true]));
        return $redirectResult;
    }
}
