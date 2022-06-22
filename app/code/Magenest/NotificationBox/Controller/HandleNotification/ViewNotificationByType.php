<?php
namespace Magenest\NotificationBox\Controller\HandleNotification;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;

class ViewNotificationByType extends Action
{
    /** @var PageFactory  */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory            = $resultPageFactory;
    }
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();

        if(isset($params['url']) && isset($params['id']))
        {
            $url = $params['url'].$params['id'];
        }
        else{
            $url = $this->_url->getUrl("notibox/customer/notification");
        }
        $resultRedirect->setPath($url);
        return $resultRedirect;
    }
}
