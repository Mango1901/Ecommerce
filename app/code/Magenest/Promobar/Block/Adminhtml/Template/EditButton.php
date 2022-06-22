<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class EditButton extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        $bar = $this->registry->registry('promobar_buttons')->getData();
        $editImage = $this->getValue();
        $height = 100;
        $width = 300;
        $border = 0;
        $borderTopLeft = 0;
        $borderTopRight = 0;
        $borderBottomLeft = 0;
        $borderBottomRight = 0;
        $paddingTop = 0;
        $paddingBottom = 0;
        $paddingRight = 0;
        $paddingLeft = 0;
        if (!empty($bar)) {
            $editButton = json_decode($bar['edit_button'], true);
            $height = $editButton['height'];
            $width = $editButton['width'];
            $border = $editButton['border'];
            $borderTopLeft = $editButton['top_left'];
            $borderTopRight = $editButton['top_right'];
            $borderBottomLeft = $editButton['bottom_left'];
            $borderBottomRight = $editButton['bottom_right'];
            if (isset($editButton['padding_top'])) {
                $paddingTop = $editButton['padding_top'];
            };
            if (isset($editButton['padding_bottom'])) {
                $paddingBottom = $editButton['padding_bottom'];
            };
            if (isset($editButton['padding_right'])) {
                $paddingRight = $editButton['padding_right'];
            };
            if (isset($editButton['padding_left'])) {
                $paddingLeft = $editButton['padding_left'];
            };

        }

        $html = '<table id="edit-button  " border="0" cellspacing="3" cellpadding="0">';
        $html .= '<tr><td style="width:15%;">' .
            '<p style="text-align:center">Height of Button</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="slider-range input-range-height-button" name="range-height-button" type="range" max="100" min="10" range="10,100" value="' . $height . '">' .
            '</div></td><td style="width:10%;">' .
            '<div class="range-slider">' .
            '<span class="range-value-height-button"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Width of Button</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-width-button" name="range-width-button" range="50,300" type="range" value="' . $width . '" min="50" max="300">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-width-button"></span>' .
            '</div></td></tr>'.


            //padding text in button top
            '<tr><td>'.
            '<p style="text-align:center">Padding Top</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-padding-top" name="range-padding-top" range="0,80" type="range" value="' . $paddingTop . '" min="0" max="80">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-padding-top"></span>' .
            '</div></td></tr>'.

            //padding text in button Bottom
            '<tr><td>'.
            '<p style="text-align:center">Padding Bottom</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-padding-bottom" name="range-padding-bottom" range="0,80" type="range" value="' . $paddingBottom . '" min="0" max="80">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-padding-bottom"></span>' .
            '</div></td></tr>'.

            //padding text in button right
            '<tr><td>'.
            '<p style="text-align:center">Padding Right</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-padding-right" name="range-padding-right" range="0,350" type="range" value="' . $paddingRight . '" min="0" max="350">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-padding-right"></span>' .
            '</div></td></tr>'.

            //padding text in button left
            '<tr><td>'.
            '<p style="text-align:center">Padding Left</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-padding-left" name="range-padding-left" range="0,350" type="range" value="' . $paddingLeft . '" min="0" max="350">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-padding-left"></span>' .
            '</div></td></tr>'.

            //Border radius of button
            '<tr><td>' .
            '<p style="text-align:center">Border Radius</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-border-radius" name="range-border-radius" range="0,60" type="range" value="' . $border . '" min="0" max="60">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-border-radius"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Border Radius Top-Left</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-border-top-left" name="range-border-top-left" range="0,60" type="range" value="' . $borderTopLeft . '" min="0" max="60">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-border-top-left"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Border Radius Bottom-Left</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-border-bottom-left" name="range-border-bottom-left" range="0,60" type="range" value="' . $borderBottomLeft . '" min="0" max="60">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-border-bottom-left"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Border Radius Top-Right</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-border-top-right" name="range-border-top-right" range="0,60" type="range" value="' . $borderTopRight . '" min="0" max="60">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-border-top-right"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Border Radius Bottom-Right</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input class="input-range-border-bottom-right" name="range-border-bottom-right" range="0,60" type="range" value="' . $borderBottomRight . '" min="0" max="60">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-border-bottom-right"></span>' .
            '</div></td></tr>' .
            '</table>';
        return $html;
    }
}
