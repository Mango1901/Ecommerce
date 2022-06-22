<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Banner;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magenest\Affiliate\Controller\Adminhtml\Banner;

/**
 * Class Create
 * @package Magenest\Affiliate\Controller\Adminhtml\Banner
 */
class Create extends Banner
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
