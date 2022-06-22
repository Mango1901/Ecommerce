<?php
namespace Magenest\NotificationBox\Controller\Adminhtml\Register;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken as CustomerTokenResource;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory;
use \Magento\Backend\App\Action\Context;

/**
 * Class AbstractMassAction
 * @package Magenest\NotificationBox\Controller\Adminhtml\NotificationType;
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{

    /** @var string */
    protected $redirectUrl = '*/report/';

    /** @var Filter */
    protected $filter;

    /** @var CollectionFactory  */
    protected $collectionFactory;

    /** @var CustomerTokenFactory */
    protected $customerTokenFactory;

    /** @var CustomerTokenResource  */
    protected $customerTokenResource;

    /**
     * AbstractMassAction constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Filter $filter
     * @param CustomerTokenResource $customerTokenResource
     * @param CustomerTokenFactory $customerTokenFactory
     */
    public function __construct(Action\Context $context,CollectionFactory $collectionFactory,Filter $filter,CustomerTokenResource $customerTokenResource,CustomerTokenFactory $customerTokenFactory )
    {
        $this->filter = $filter;
        $this->customerTokenResource = $customerTokenResource;
        $this->customerTokenFactory = $customerTokenFactory;
        $this->collectionFactory = $collectionFactory;

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
        return $this->_authorization->isAllowed('Magenest_NotificationBox::report');
    }
}
