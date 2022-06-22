<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MapList\Block\Adminhtml\Template;

use Magento\Framework\Registry;

class Gallery extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    public function getElementHtml()
    {
        $gallery = $this->getValue();
        $html = '<table id="gallery" class="gallery" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<thead id="gallery_thead" class="gallery">' .
            '<tr class="gallery">' .
            '<div class="image image-placeholder" id="gallery-image-location">' .
            '<div class="product-image-wrapper">' .
            '<p class="image-placeholder-text">Browse to find or drag image here</p>' .
            '<input id="magenest-upload-image-gallery" type="file" name="gallery_0" multiple="multiple">' .
            '</div></div></tr></thead></table>';
        return $html;
    }
}
