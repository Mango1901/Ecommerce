<?php


namespace Magenest\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Model\AccountFactory;

/**
 * Class Account
 * @package Magenest\Affiliate\Controller\Adminhtml
 */
abstract class Account extends AbstractAction
{
    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * Account constructor.
     *
     * @param Context $context
     * @param AccountFactory $accountFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        AccountFactory $accountFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_accountFactory = $accountFactory;

        parent::__construct($context, $resultPageFactory, $coreRegistry);
    }

    /**
     * @return mixed
     */
    protected function _initAccount()
    {
        $accountId = (int)$this->getRequest()->getParam('id');
        /** @var \Magenest\Affiliate\Model\Account $account */
        $account = $this->_accountFactory->create();
        if ($accountId) {
            $account->load($accountId);
            if (!$account->getId()) {
                $this->messageManager->addError(__('This account no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('affiliate/account/index');

                return $resultRedirect;
            }
        }

        return $account;
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Affiliate::account');
    }
}
