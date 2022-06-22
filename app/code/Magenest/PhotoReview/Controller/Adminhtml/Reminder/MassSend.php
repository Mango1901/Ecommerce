<?php
namespace Magenest\PhotoReview\Controller\Adminhtml\Reminder;

use Magenest\PhotoReview\Model\Cron;
use Magenest\PhotoReview\Model\ResourceModel\ReminderEmail;
use Magenest\PhotoReview\Model\ReminderEmail as ReminderEmailModel;
use Magento\Backend\App\Action;
use Psr\Log\LoggerInterface;

class MassSend extends \Magenest\PhotoReview\Controller\Adminhtml\Reminder\Index
{
    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail\CollectionFactory  */
    protected $_reminderEmailCollection;

    /** @var ReminderEmail  */
    protected $_reminderEmailResource;

    /** @var Cron  */
    protected $cronModel;

    /** @var  \Psr\Log\LoggerInterface */
    protected $_logger;

    /** @var \Magento\Ui\Component\MassAction\Filter */
    protected $_filter;

    public function __construct(
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail\CollectionFactory $collectionFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail $reminderEmailResource,
        \Magenest\PhotoReview\Model\Cron $cronModel,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Backend\App\Action\Context $context
    ){
        $this->_reminderEmailCollection = $collectionFactory;
        $this->_reminderEmailResource = $reminderEmailResource;
        $this->cronModel = $cronModel;
        $this->_logger = $logger;
        $this->_filter = $filter;
        parent::__construct($pageFactory, $context);
    }

    public function execute()
    {
        try{
            $collections = $this->_filter->getCollection($this->_reminderEmailCollection->create());
            $count = 0;
            $ids = [];
            foreach ($collections as $collection){
                $data = $collection->getData();
                $this->cronModel->sendEmail($data);
                $ids[] = $collection->getId();
                $count++;
            }
            if(!empty($ids)){
                $this->_reminderEmailResource->updateMultiStatus($ids, ReminderEmailModel::STATUS_SENT);
            }
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been sent.', $count));
        }catch (\Exception $exception){
            $this->messageManager->addErrorMessage($exception->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/index');
    }
}