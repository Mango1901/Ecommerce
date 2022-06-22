<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class PositionText extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        if (!empty($bar)) {
            if (array_key_exists('height-pro-bar', $bar)) {
                $height_bar = $bar['height-pro-bar'];
            }
            else{
                $height_bar = '65';
            }
        } else {
            $height_bar = '65';
        }
        $positionText = $this->getValue();
        $html = '<table id="edit-height" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<tr><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="desktop-input-range-position-text" name="position_text" class="input-range-position-text" type="range" range="-50,50" value="0" min="-50" max="50">' .
            '</div></td><td style="width:10%;">' .
            '<div class="range-slider">' .
            '<span class="range-value-position-text"></span>' .
            '</div></td></tr></table>';
        return $html;
    }
}