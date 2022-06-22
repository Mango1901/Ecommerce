<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 29/10/2020 15:56
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magenest\RewardPoints\Controller\Adminhtml\Membership;
use Magento\Framework\App\ResponseInterface;

class Index extends Membership
{

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultPage = $this->_initAction();
        $resultPage->getConfig()->getTitle()->prepend(__('Membership Groups'));

        return $resultPage;
    }
}