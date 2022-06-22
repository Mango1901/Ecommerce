<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Account;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\Affiliate\Model\Account;
use Magenest\Affiliate\Model\ResourceModel\Account\CollectionFactory;

/**
 * Class MassDelete
 * @package Magenest\Affiliate\Controller\Adminhtml\Account
 */
class MassDelete extends Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Account
     */
    protected $account;

    /**
     * MassDelete constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param Account $account
     * @param Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        Account $account,
        Context $context
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->account = $account;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $delete = 0;
        foreach ($collection as $item) {
            /** @var Account $item */
            try {
                $affiliateAccount = $this->account->getCollection()
                    ->addFieldToFilter('parent', ['eq' => $item->getId()]);

                if ($affiliateAccount->getSize()) {
                    $this->messageManager->addError(__('Can\'t remove the Parent Affiliate ID %1. Please remove child before.', $item->getId()));

                    return $resultRedirect->setPath('*/*/');
                }

                $item->delete();
                $delete++;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $delete));

        return $resultRedirect->setPath('*/*/');
    }
}
