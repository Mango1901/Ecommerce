<?php
namespace Magenest\SocialLogin\Controller\Adminhtml\SocialAccount;

use Magento\Backend\App\Action;

/**
 * Class Index
 * @package Magenest\SocialLogin\Controller\Adminhtml\SocialAccount
 */
class Index extends Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageResult;

    /**
     * Index constructor.
     * @param \Magento\Framework\View\Result\PageFactory $pageResult
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $pageResult,
        \Magento\Backend\App\Action\Context $context)
    {
        parent::__construct($context);
        $this->pageResult = $pageResult;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->pageResult->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Social Accounts'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_SocialLogin::menu_sociallogin');
    }
}