<?php

namespace Magenest\FastErp\Controller\Adminhtml\ErpHistory;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Magenest\AdminActivity\Controller\Adminhtml\Activity
 */
class Index extends Action
{
    /**
     * @var string
     */
    const ADMIN_RESOURCE = 'Magenest_FastErp::erp_history_log';

    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /**
     * Index constructor.
     *
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
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_FastErp::erp_history_log');
        $resultPage->addBreadcrumb(__('Magenest'), __('ERP History Log'));
        $resultPage->getConfig()->getTitle()->prepend(__('ERP History Log'));

        return $resultPage;
    }
}
