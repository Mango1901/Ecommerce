<?php

namespace Magenest\MegaMenu\Controller\Adminhtml\Label;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    protected $pageFactory;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->pageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('Magenest_MegaMenu::label');
        $resultPage->getConfig()->getTitle()->prepend(__('Labels'));

        return $resultPage;
    }
}
