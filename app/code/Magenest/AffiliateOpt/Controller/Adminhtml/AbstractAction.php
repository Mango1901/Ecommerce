<?php

namespace Magenest\AffiliateOpt\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Phrase;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magenest\AffiliateOpt\Helper\Data as HelperData;

/**
 * Class AbstractAction
 * @package Magenest\AffiliateOpt\Controller\Adminhtml
 */
abstract class AbstractAction extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * AbstractAction constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param HelperData $helperData
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        HelperData $helperData
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->helperData         = $helperData;

        parent::__construct($context);
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        if ($this->helperData->canUseStoreSwitcherLayoutByMpReports()) {
            $resultPage->addHandle('store_switcher');
        }

        $resultPage->getConfig()->getTitle()->prepend($this->getPageTitle());

        return $resultPage;
    }

    /**
     * @return Phrase
     */
    public function getPageTitle()
    {
        return __('Accounts Reports');
    }
}
