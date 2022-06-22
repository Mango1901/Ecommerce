<?php
namespace Magenest\NotificationBox\Controller\Adminhtml\Notification;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\NotificationBox\Model\ResourceModel\Notification\CollectionFactory;
use Magenest\NotificationBox\Model\ResourceModel\Notification as NotificationResource;
use Magenest\NotificationBox\Model\NotificationFactory;
use \Magento\Backend\App\Action\Context;

/**
 * Class AbstractMassAction
 * @package Magenest\NotificationBox\Controller\Adminhtml\Notification;
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{

    /** @var string */
    protected $redirectUrl = '*/*/';

    /** @var Filter */
    protected $filter;

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /** @var NotificationFactory */
    protected $notificationFactory;

    /** @var NotificationResource  */
    protected $notificationResource;

    /**
     * AbstractMassAction constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param NotificationResource $notificationResource
     * @param NotificationFactory $notificationFactory
     * @param Filter $filter
     * @param Context $context
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        NotificationResource $notificationResource,
        NotificationFactory $notificationFactory,
        Filter $filter,
        Context $context
    ){
        $this->collectionFactory = $collectionFactory;
        $this->notificationResource = $notificationResource;
        $this->notificationFactory = $notificationFactory;
        $this->filter = $filter;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Set status to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract protected function massAction(AbstractCollection $collection);

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_NotificationBox::notification');
    }
}
