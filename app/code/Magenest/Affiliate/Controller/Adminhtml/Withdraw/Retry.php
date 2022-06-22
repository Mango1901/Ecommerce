<?php

namespace Magenest\Affiliate\Controller\Adminhtml\Withdraw;

use Magenest\Affiliate\Model\Withdraw\Status;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magenest\PaymentEPay\Api\Data\HandleDisbursementInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Withdraw;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 */
class Retry extends Withdraw
{
    /**
     * @var HandleDisbursementInterface
     */
    protected $_handleDisbursement;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param WithdrawFactory $withdrawFactory
     * @param HandleDisbursementInterface $handleDisbursement
     */
    public function __construct(
        Context $context, PageFactory
        $resultPageFactory, Registry $coreRegistry,
        WithdrawFactory $withdrawFactory,
        HandleDisbursementInterface $handleDisbursement
    ) {
        $this->_handleDisbursement = $handleDisbursement;
        parent::__construct($context, $resultPageFactory, $coreRegistry, $withdrawFactory);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        /** @var \Magenest\Affiliate\Model\Withdraw $withdraw */
        $withdraw = $this->_initWithdraw();

        try {
            if ($withdraw->getStatus() == Status::FAILED) {
                $result = $this->_handleDisbursement->execute($withdraw);
                if (isset($result['ResponseCode']) && $result['ResponseCode'] === 200) {
                    $withdraw->setData('status', Status::COMPLETE);
                    $this->messageManager->addSuccess(__('The withdraw has been retried successfully.'));
                } else {
                    $withdraw->setData('status', Status::FAILED);
                    $this->messageManager->addErrorMessage(__('Retry failed. Please confirm bank account information of customer or balance of merchant'));
                }
                $withdraw->save();
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __($e->getMessage())
            );
        }
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
