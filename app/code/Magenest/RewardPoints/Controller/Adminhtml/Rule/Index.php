<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Rule;

use Magento\Backend\App\Action;
use Magenest\RewardPoints\Controller\Adminhtml\Rule;

/**
 * Class Index
 * @package Magenest\RewardPoints\Controller\Adminhtml\Rule
 */
class Index extends Rule
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Rules Manager'));

        return $resultPage;
    }
}
