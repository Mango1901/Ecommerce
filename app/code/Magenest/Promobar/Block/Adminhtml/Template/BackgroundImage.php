<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

class BackgroundImage  extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $bkgImage = $this->getValue();
        $html = '<table id="bkgImage" class="bkgImage" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<thead id="bkgImage_thead" class="bkgImage">' .
            '<tr class="bkgImage">' .
                '<div class="image image-placeholder" id="bkgImage-location">' .
                    '<div class="product-image-wrapper">' .
                        '<p class="image-placeholder-text">Browse to find or drag image here</p>' .
                        '<input id="magenest-upload-image-bkgImage" type="file" name="bkgImage">' .
            '</div></div></tr></thead></table>';
        return $html;
    }
}