<?php


namespace Magenest\Affiliate\Controller\Account\Withdraw;

use Magenest\Affiliate\Controller\Account;

/**
 * Class View
 * @package Magenest\Affiliate\Controller\Account\Withdraw
 */
class View extends Account
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $customerId = $this->customerSession->getId();
        $id         = $this->getRequest()->getParam('id');
        $withdraw   = $this->withdrawFactory->create()->load($id);

        if (!$withdraw || !$withdraw->getId() || $withdraw->getCustomerId() !== $customerId) {
            $this->messageManager->addErrorMessage(__('Cannot find item.'));

            return $this->_redirect('*/account/withdraw');
        }

        $this->registry->register('withdraw_view_data', $withdraw);

        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
