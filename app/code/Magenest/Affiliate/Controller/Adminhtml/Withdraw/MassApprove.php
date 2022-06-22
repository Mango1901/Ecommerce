<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Withdraw;

use Exception;
use Magenest\PaymentEPay\Api\Data\HandleDisbursementInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\Affiliate\Model\ResourceModel\Withdraw\CollectionFactory;
use Magenest\Affiliate\Model\ResourceModel\Withdraw\WithdrawFactory;
use Magenest\Affiliate\Model\Withdraw;
use Magenest\Affiliate\Model\Withdraw\Status;

/**
 * Class MassApprove
 * @package Magenest\Affiliate\Controller\Adminhtml\Withdraw
 */
class MassApprove extends Action
{
    /**
     * @var Filter
     */
    protected $_filter;

    /**
     * @var CollectionFactory|WithdrawFactory
     */
    protected $_collectionFactory;

    /**
     * @var HandleDisbursementInterface
     */
    protected $_handleDisbursement;

    /**
     * MassApprove constructor.
     *
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param HandleDisbursementInterface $handleDisbursement
     * @param Context $context
     */
    public function __construct(
        Filter $filter,
        CollectionFactory $collectionFactory,
        HandleDisbursementInterface $handleDisbursement,
        Context $context
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_handleDisbursement = $handleDisbursement;
        parent::__construct($context);
    }

    /**
     * @return $this|ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());
        $approve = 0;
        foreach ($collection as $withdraw) {
            /** @var Withdraw $withdraw */
            try {
                if ($withdraw->getStatus() != Status::CANCEL) {
                    $result = $this->_handleDisbursement->execute($withdraw);
                    if (isset($result['ResponseCode']) && $result['ResponseCode'] === 200) {
                        $withdraw->setData('status', Status::COMPLETE);
                    } else {
                        $withdraw->setData('status', Status::FAILED);
                    }
                    $withdraw->save();
                    $approve++;
                }
            } catch (Exception $e) {
                $this->messageManager->addError(
                    __($e->getMessage())
                );
            }
        }
        $this->messageManager->addSuccessMessage(__(
            'A total of %1 record(s) have been approved successfully.',
            $approve
        ));
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
