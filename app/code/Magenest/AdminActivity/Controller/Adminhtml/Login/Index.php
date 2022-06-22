<?php

namespace Magenest\AdminActivity\Controller\Adminhtml\Login;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 *
 * @package Magenest\AdminActivity\Controller\Adminhtml\Login
 */
class Index extends Action
{
	/**
	 * @var string
	 */
	const ADMIN_RESOURCE = 'Magenest_AdminActivity::login_activity';

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
		$resultPage->setActiveMenu('Magenest_AdminActivity::login_activity');
		$resultPage->addBreadcrumb(__('Magenest'), __('Login Activity'));
		$resultPage->getConfig()->getTitle()->prepend(__('Login Activity'));

		return $resultPage;
	}
}
