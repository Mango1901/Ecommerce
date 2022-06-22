<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Controller\Adminhtml\Salesoverview;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Aheadworks\AdvancedReports\Controller\Adminhtml\Salesoverview
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReports::reports_salesoverview';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
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
        $paymentTypeName = $this->_request->getParam('payment_name');
        $couponCode = $this->_request->getParam('coupon_code');
        if ($couponCode) {
            $title = __('Sales Overview (%1)', base64_decode($couponCode));
        } elseif ($paymentTypeName) {
            $title = __('Sales Overview (%1)', base64_decode($paymentTypeName));
        } else {
            $title = __('Sales Overview');
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_AdvancedReports::reports_salesoverview');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
