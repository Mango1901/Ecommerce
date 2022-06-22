<?php
namespace Magenest\PhotoReview\Controller\Adminhtml\Logcron;

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
        $resultPage->addBreadcrumb(__('Logs Cron'), __('Logs Cron'));
        $resultPage->getConfig()->getTitle()->prepend(__('Logs Cron'));
        return $resultPage;
    }
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_PhotoReview::logcron');
    }
}