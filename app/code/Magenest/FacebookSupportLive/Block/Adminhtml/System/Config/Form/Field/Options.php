<?php
/**
 * Copyright (c) Magenest, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\FacebookSupportLive\Block\Adminhtml\System\Config\Form\Field;

class Options extends \Magento\Framework\View\Element\Html\Select
{

    const OPTION_FACEBOOK =
        [
            'theme_color',
            'logged_in_greeting',
            'logged_out_greeting',
            'greeting_dialog_display',
            'greeting_dialog_delay',
            'minimized'
        ];

    /**
     * @param $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        foreach (self::OPTION_FACEBOOK as $option) {
            $this->addOption($option, $option);
        }
        return parent::_toHtml();
    }
}
