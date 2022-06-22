<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class EditImage extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        $editImage = $this->getValue();
        $height = 100;
        $width = 100;
        $left_right = 0;
        $up_down = 0;
        $opacity = 1;
        if(!empty($bar)){
            if(array_key_exists('edit_background',$bar)) {
                $editBackground = json_decode($bar['edit_background'], true);
                $height = $editBackground['height'];
                $width = $editBackground['width'];
                $left_right = $editBackground['left-right'];
                $up_down = $editBackground['up-down'];
                $opacity = $editBackground['opacity'];
            }
        }
        $html = '<table id="edit-image" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<tr><td style="width:15%;">'.
            '<p style="text-align:center">Height</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="desktop-input-range-height" class="input-range-height" name="range-height" type="range" range="100,600" max=600 min=100 value="'.$height.'">' .
            '</div></td><td style="width:10%;">' .
            '<div class="range-slider">'.
            '<span class="range-value-height"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Width</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="desktop-input-range-width" class="input-range-width" name="range-width" type="range" range="100,600" value="'.$width.'" min="100" max="600">' .
            '</div></td><td>'.
            '<div class="range-slider">'.
            '<span class="range-value-width"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Left & Right</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="desktop-input-range-left-right" class="input-range-left-right" name="range-left-right" type="range" range="-50,50" value="'.$left_right.'" min="-50" max="50">' .
            '</div></td><td>' .
            '<div class="range-slider">'.
            '<span class="range-value-left-right"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Up & Down</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="desktop-input-range-up-down" class="input-range-up-down" name="range-up-down" type="range" range="-50,50" value="'.$up_down.'" min="-50" max="50">' .
            '</div></td><td>' .
            '<div class="range-slider">'.
            '<span class="range-value-up-down"></span>' .
            '</div></td></tr><tr><td>' .
            '<p style="text-align:center">Opacity</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="desktop-input-range-opacity" class="input-range-opacity" name="range-opacity" type="range" range="0,2" value="'.$opacity.'" min="0" max="1" step="0.01">' .
            '</div></td><td>' .
            '<div class="range-slider">'.
            '<span class="range-value-opacity"></span>' .
            '</div></td></tr>' .
            '</table>';
        return $html;
    }
}