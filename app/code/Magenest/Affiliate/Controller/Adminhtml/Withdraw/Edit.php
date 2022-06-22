<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Withdraw;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Withdraw;

/**
 * Class Edit
 * @package Magenest\Affiliate\Controller\Adminhtml\Withdraw
 */
class Edit extends Withdraw
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|Page
     */
    public function execute()
    {
        /** @var \Magenest\Affiliate\Model\Withdraw $withdraw */
        $withdraw = $this->_initWithdraw();

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::withdraw');
        $resultPage->getConfig()->getTitle()->set(__('Withdraws'));

        $title = $withdraw->getId() ? __('Edit Withdraw "#%1"', $withdraw->getId()) : __('New Withdraw');
        $resultPage->getConfig()->getTitle()->prepend($title);
        $this->_coreRegistry->register('current_withdraw', $withdraw);

        return $resultPage;
    }
}
