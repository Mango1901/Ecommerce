<?php
namespace Magenest\PhotoReview\Controller\Adminhtml\Reminder;

class Delete extends \Magenest\PhotoReview\Controller\Adminhtml\Reminder\Index
{
    protected $_reminderEmailFactory;
    protected $_reminderEmailResource;

    public function __construct(
        \Magenest\PhotoReview\Model\ReminderEmailFactory $reminderEmailFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail $reminderEmailResource,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_reminderEmailFactory = $reminderEmailFactory;
        $this->_reminderEmailResource = $reminderEmailResource;
        parent::__construct($pageFactory, $context);
    }
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if($id){
            $reminderEmailModel = $this->_reminderEmailFactory->create();
            $this->_reminderEmailResource->load($reminderEmailModel,$id);
            if(!$reminderEmailModel->getId()){
                $this->messageManager->addErrorMessage(__('This record doesn\'t exist'));
            }else{
                $email = $reminderEmailModel->getEmail();
                $this->_reminderEmailResource->delete($reminderEmailModel);
                $this->messageManager->addSuccessMessage(__('Email sent to %1 was deleted',$email));
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/index');
        }
    }
}