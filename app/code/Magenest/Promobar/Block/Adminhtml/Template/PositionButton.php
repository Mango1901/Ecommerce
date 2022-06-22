<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class PositionButton extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        $positionButton = $this->getValue();
        $positionLeft = 0;
        $positionRight = 0;
        $upDown = 0;
        $button = [];
        if (!empty($bar)) {
            if (array_key_exists('multiple_content', $bar)) {
                $infoButton = json_decode($bar['multiple_content'], true);
                $arrayInfo = reset($infoButton);
                if ($arrayInfo['button'] != null) {
                    $button = json_decode($arrayInfo['button'], true);
                }
                if (!empty($button)) {
                    if ($button['button']['data']['displayLeft'] !== 'no check') {
                        $positionLeft = str_replace("%", "", $button['button']['data']['displayLeft']);
                    } elseif ($button['button']['data']['displayRight'] !== 'no check') {
                        $positionRight = str_replace("%", "", $button['button']['data']['displayRight']);
                    }
                    $upDown = str_replace("px", "", $button['button']['data']['upDown']);
                }
            }
        }
        $html = '<table id="position-button" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<tr><td style="width:5%;">' .
            '<input id="check-range-left" style="margin:15px 10px 10px 15px;" type="checkbox" name="desktop-checkbox-left" value="0" disabled>' .
            '</td><td style="width:20%;">' .
            '<p style="text-align:center">Show on the left</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width:100%">' .
            '<input id="desktop-input-range-left" class="input-range-left" name="range-button-left" type="range" range="0,50" value="' . $positionLeft . '" min="0" max="50" style="display:none">' .
            '</div></td><td style="width:10%;">' .
            '<div class="range-slider">' .
            '<span class="range-value-left" style="display:none"></span>' .
            '</div></td></tr><tr><td>' .
            '<input id="check-range-right" style="margin:15px 10px 10px 15px;" type="checkbox" name="desktop-checkbox-right" value="1" disabled>' .
            '</td><td>' .
            '<p style="text-align:center">Show on the right</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width:100%">' .
            '<input id="desktop-input-range-right" class="input-range-right" name="range-button-right" type="range" range="0,50" value="' . $positionRight . '" min="0" max="50" style="display:none">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-right" style="display:none"></span>' .
            '</div></td></tr><tr><td>' .
            '</td><td>' .
            '<p style="text-align:center">Up-Down</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="desktop-input-range-button-up-down" class="input-range-button-up-down" name="range-button-up-down" type="range" range="-30,30" value="' . $upDown . '" min="-30" max="30">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span class="range-value-button-up-down"></span>' .
            '</div></td></tr>' .
            '</table>';
        return $html;
    }
}