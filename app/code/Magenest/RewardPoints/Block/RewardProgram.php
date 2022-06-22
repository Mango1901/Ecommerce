<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 28/12/2020 09:21
 */

namespace Magenest\RewardPoints\Block;

use Magento\Framework\View\Element\Html\Link\Current;

class RewardProgram extends Current
{
    protected function _toHtml()
    {
        if ($this->_scopeConfig->isSetFlag(\Magenest\RewardPoints\Helper\Data::XML_PATH_MEMBERSHIP_STATUS)
            && $this->_scopeConfig->isSetFlag(\Magenest\RewardPoints\Helper\Data::XML_PATH_MODULE_ENABLE)) {
            return parent::_toHtml();
        }

        return '';
    }
}