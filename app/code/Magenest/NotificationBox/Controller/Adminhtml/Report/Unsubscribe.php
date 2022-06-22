<?php

namespace Magenest\NotificationBox\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;

class Unsubscribe extends \Magento\Backend\App\Action
{
    /** @var CustomerTokenFactory  */
    protected $customerTokenFactory;

    /** @var CustomerToken  */
    protected $customerToken;

    /**
     * @param Action\Context $context
     * @param CustomerTokenFactory $customerTokenFactory
     * @param CustomerToken $customerToken
     */
    public function __construct(
        Action\Context $context,
        CustomerTokenFactory $customerTokenFactory,
        CustomerToken $customerToken
    )
    {
        $this->customerToken = $customerToken;
        $this->customerTokenFactory = $customerTokenFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('entity_id');
        if ($id) {
            try {
                $customerTokenModel = $this->customerTokenFactory->create();
                $this->customerToken->load($customerTokenModel,$id);
                $customerTokenModel->setData('status', \Magenest\NotificationBox\Model\CustomerToken::STATUS_UNSUBSCRIBED);
                $this->customerToken->save($customerTokenModel);
                $this->messageManager->addSuccessMessage(__("The register has been unsubscribe."));
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __("An error occurred when unsubscribing the registry"));
            }
        }
        return $this->_redirect('notibox/report/index');
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_NotificationBox::report');
    }
}
