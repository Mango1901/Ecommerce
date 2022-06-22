<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 19/11/2020 10:28
 */

namespace Magenest\RewardPoints\Block\Adminhtml\Membership;

use Magento\Backend\Block\Template;

class ListCustomerAssigned extends Template
{
    /**
     * @var \Magento\Backend\Model\Session\Proxy
     */
    private $backendSession;

    /**
     * ListCustomerAssigned constructor.
     * @param Template\Context $context
     * @param $session
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        $session,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->backendSession = $session;
    }

    /**
     * @return int|void
     */
    public function countCustomerSelected()
    {
        return count($this->backendSession->getAddPointsCustomers());
    }
}