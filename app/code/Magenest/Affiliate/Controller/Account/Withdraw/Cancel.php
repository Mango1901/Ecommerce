<?php


namespace Magenest\Affiliate\Controller\Account\Withdraw;

use Exception;
use Magenest\Affiliate\Controller\Account;

/**
 * Class Cancel
 * @package Magenest\Affiliate\Controller\Account\Withdraw
 */
class Cancel extends Account
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $customerId = $this->customerSession->getId();
        $id         = $this->getRequest()->getParam('id');

        try {
            $withdraw = $this->withdrawFactory->create()->load($id);
            if ($withdraw->getId() && $withdraw->getCustomerId() === $customerId) {
                $withdraw->cancel();
                $this->messageManager->addSuccessMessage(__('The withdraw has been canceled successfully.'));
            } else {
                $this->messageManager->addErrorMessage(__('Cannot find item.'));
            }
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the request.'));
        }

        return $this->_redirect('affiliate/account/withdraw');
    }
}
