<?php
namespace Magenest\PhotoReview\Controller\Adminhtml\Reminder;

class Preview extends \Magenest\PhotoReview\Controller\Adminhtml\Reminder\Index
{
    /** @var \Magenest\PhotoReview\Model\ReminderEmailFactory  */
    protected $_reminderEmailFactory;
    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail  */
    protected $_reminderEmailResource;
    protected $_photoReviewSession;

    public function __construct(
        \Magenest\PhotoReview\Model\ReminderEmailFactory $reminderEmailFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail $reminderEmailResource,
        \Magenest\PhotoReview\Model\Session $photoReviewSession,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_reminderEmailFactory = $reminderEmailFactory;
        $this->_reminderEmailResource = $reminderEmailResource;
        $this->_photoReviewSession = $photoReviewSession;
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
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/index');
            }
            $this->_photoReviewSession->setData('reminder_email', $reminderEmailModel);
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->_resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__('Send to %1', $reminderEmailModel->getEmail()));
            return $resultPage;
        }
    }
}