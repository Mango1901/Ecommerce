<?php
namespace Magenest\PhotoReview\Controller\Adminhtml\Reminder;

class Index extends \Magento\Backend\App\Action
{
    /** @var \Magento\Framework\View\Result\PageFactory $_resultPageFactory */
    protected $_resultPageFactory;

    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_resultPageFactory = $pageFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->addBreadcrumb(__('Reminder Email Logs'), __('Reminder Email Logs'));
        $resultPage->getConfig()->getTitle()->prepend(__('Reminder Email Logs'));
        return $resultPage;
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_PhotoReview::reminder');
    }
}