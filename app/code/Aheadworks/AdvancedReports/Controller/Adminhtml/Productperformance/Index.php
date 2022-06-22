<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Controller\Adminhtml\Productperformance;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Aheadworks\AdvancedReports\Controller\Adminhtml\Productperformance
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aheadworks_AdvancedReports::reports_productperformance';

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
        $couponCode = $this->_request->getParam('coupon_code');
        $manufacturer = $this->_request->getParam('manufacturer');
        $paymentName = $this->_request->getParam('payment_name');
        $categoryName = $this->_request->getParam('category_name');
        if ($couponCode) {
            $title = __('Product Performance (%1)', base64_decode($couponCode));
        } elseif ($manufacturer) {
            $title = __('Product Performance (%1)', base64_decode($manufacturer));
        } elseif ($paymentName) {
            $title = __('Product Performance (%1)', base64_decode($paymentName));
        } elseif ($categoryName) {
            $title = __('Product Performance (%1)', base64_decode($categoryName));
        } else {
            $title = __('Product Performance');
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Aheadworks_AdvancedReports::reports_productperformance');
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}
