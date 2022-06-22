<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 04/11/2020 13:57
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Membership;

use Magento\Catalog\Controller\Adminhtml\Category\Image\Upload;

class TierLogoUploader extends Upload
{
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_RewardPoints::system_rewardpoints_membership');
    }
}