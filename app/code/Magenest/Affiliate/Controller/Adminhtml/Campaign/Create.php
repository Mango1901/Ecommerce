<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Campaign;

use Magenest\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class Create
 * @package Magenest\Affiliate\Controller\Adminhtml\Campaign
 */
class Create extends Campaign
{
    /**
     * Create new campaign
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
