<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Account;

use Magento\Backend\App\Action;
use Magenest\RewardPoints\Controller\Adminhtml\Account;

/**
 * Class Index
 * @package Magenest\RewardPoints\Controller\Adminhtml\Account
 */
class Index extends Account
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Points Management'));

        return $resultPage;
    }
}
