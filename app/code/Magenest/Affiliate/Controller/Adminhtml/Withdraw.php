<?php


namespace Magenest\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Model\WithdrawFactory;

/**
 * Class Withdraw
 * @package Magenest\Affiliate\Controller\Adminhtml
 */
abstract class Withdraw extends AbstractAction
{
    /**
     * @var WithdrawFactory
     */
    protected $_withdrawFactory;

    /**
     * Withdraw constructor.
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Registry $coreRegistry
     * @param WithdrawFactory $withdrawFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Registry $coreRegistry,
        WithdrawFactory $withdrawFactory
    ) {
        $this->_withdrawFactory = $withdrawFactory;

        parent::__construct($context, $resultPageFactory, $coreRegistry);
    }

    /**
     * @return mixed
     */
    protected function _initWithdraw()
    {
        $withdrawId = (int)$this->getRequest()->getParam('id');
        /** @var \Magenest\Affiliate\Model\Withdraw $withdraw */
        $withdraw = $this->_withdrawFactory->create();

        if ($withdrawId) {
            $withdraw->load($withdrawId);
            if (!$withdraw->getId()) {
                $this->messageManager->addErrorMessage(__('This withdrawal no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('affiliate/withdraw/index');

                return $resultRedirect;
            }
        }

        return $withdraw;
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Affiliate::withdraw');
    }
}
