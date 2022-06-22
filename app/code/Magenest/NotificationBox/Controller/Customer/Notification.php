<?php
namespace Magenest\NotificationBox\Controller\Customer;
use Magenest\NotificationBox\Helper\Helper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

class Notification extends Action {

    /** @var Helper  */
    protected $helper;

    /** @var ResultFactory  */
    protected $resultFactory;

    /**
     * @param Context $context
     * @param ResultFactory $resultFactory
     * @param Helper $helper
     */
    public function __construct(Context $context,ResultFactory $resultFactory,Helper $helper)
    {
        $this->helper = $helper;
        $this->resultFactory = $resultFactory;
        parent::__construct($context);
    }

    public function execute() {
        if(!$this->helper->getEnableModule()){
            $redirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $redirect->setPath('no-route');
            return $redirect;
        }
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
