<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Transaction;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\Affiliate\Model\ResourceModel\Transaction\CollectionFactory;
use Magenest\Affiliate\Model\Transaction\Type;

/**
 * Class MassCancel
 * @package Magenest\Affiliate\Controller\Adminhtml\Transaction
 */
class MassCancel extends Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory
     */
    protected $_transactionFactory;

    /**
     * MassCancel constructor.
     *
     * @param Context $context
     * @param CollectionFactory $transactionFactory
     * @param Filter $filter
     */
    public function __construct(
        Context $context,
        CollectionFactory $transactionFactory,
        Filter $filter
    ) {
        $this->_transactionFactory = $transactionFactory;
        $this->_filter = $filter;

        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_transactionFactory->create());
        $transactionCanceled = 0;

        foreach ($collection->getItems() as $transaction) {
            try {
                /** only cancel transaction type order invoice*/
                if ($transaction->getType() != Type::PAID) {
                    $transactionCanceled++;
                    $transaction->cancel();
                }
            } catch (Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
            }
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been checked.', $transactionCanceled)
        );

        return $this->resultRedirectFactory->create()->setPath('affiliate/*/');
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Affiliate::transaction');
    }
}
