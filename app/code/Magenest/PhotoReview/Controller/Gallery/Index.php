<?php
namespace Magenest\PhotoReview\Controller\Gallery;

use Magenest\PhotoReview\Helper\Data;
use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    protected $resultPageFactory;

    /** @var \Magenest\PhotoReview\Helper\Data  */
    protected $_helperData;

    public function __construct(
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Action\Context $context
    ){
        $this->_helperData = $helperData;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $title = $this->_helperData->getScopeConfig(Data::MENU_TITLE);
        if($title == ""){
            $title = __('All Review');
        }
        $resultPage->getConfig()->getTitle()->prepend($title);
        return $resultPage;
    }
}