<?php
namespace Magenest\PhotoReview\Block\Adminhtml\Reminder;

class Preview extends \Magento\Backend\Block\Template
{
    /** @var \Magenest\PhotoReview\Model\Session  */
    protected $_photoReviewSession;

    public function __construct(
        \Magenest\PhotoReview\Model\Session $photoReviewSession,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ){
        $this->_photoReviewSession = $photoReviewSession;
        parent::__construct($context, $data);
    }
    public function getReminderEmail()
    {
        /** @var \Magenest\PhotoReview\Model\ReminderEmail $reminderEmailModel */
        $reminderEmailModel = $this->_photoReviewSession->getData('reminder_email');
        $content = $reminderEmailModel->getContent();
        return $content;
    }
}