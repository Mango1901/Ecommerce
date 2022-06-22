<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template\Mobile;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class MobilePositionText extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    protected $registry;

    public function __construct(Factory $factoryElement,
                                CollectionFactory $factoryCollection,
                                Escaper $escaper,
                                \Magento\Framework\Registry $registry,
                                array $data = [])
    {
        $this->registry = $registry;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    public function getElementHtml()
    {
        $bar = $this->registry->registry('promobar_promobars')->getData();
        $mobileBar = $this->registry->registry('promobar_mobile_promobars')->getData();
        if (!empty($mobileBar)) {
            $height_bar = $mobileBar['mobile_height_pro_bar'];
            $positionText = json_decode(json_decode($mobileBar['mobile_multiple_content'], true)[0]['content'], true)['positionText'];
            $positionText = str_replace("px", "", $positionText);
        } else {
            $positionText = '0';
        }
        $html = '<table id="edit-height" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<tr><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="mobile-input-range-position-text" name="mobile_position_text" class="input-range-position-text" type="range" range="-50,50" value="' . $positionText . '" min="-50" max="50">' .
            '</div></td><td style="width:10%;">' .
            '<div class="range-slider">' .
            '<span id="mobile-range-value-position-text"></span>' .
            '</div></td></tr></table>';
        return $html;
    }
}