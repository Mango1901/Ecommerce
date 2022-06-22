<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Withdraw;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magenest\Affiliate\Controller\Adminhtml\Withdraw;

/**
 * Class Create
 * @package Magenest\Affiliate\Controller\Adminhtml\Withdraw
 */
class Create extends Withdraw
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
