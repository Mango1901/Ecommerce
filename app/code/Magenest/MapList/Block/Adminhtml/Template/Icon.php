<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MapList\Block\Adminhtml\Template;

use Magento\Framework\Registry;

class Icon extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $icon = $this->getValue();
        $html = '<table id="icon" class="icon" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<thead id="icon_thead" class="icon">' .
            '<tr class="icon">' .
            '<div class="image image-placeholder" id="icon-location">' .
            '<div class="product-image-wrapper">' .
            '<p class="image-placeholder-text">Browse to find or drag image here</p>' .
            '<input id="magenest-upload-image-icon" type="file" name="icon_0" multiple="multiple">' .
            '</div></div></tr></thead></table>';
        return $html;
    }
}
