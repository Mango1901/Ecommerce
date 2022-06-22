<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template\Mobile;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class MobilePositionButton extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        $positionButton = $this->getValue();
        $positionLeft = 0;
        $positionRight = 0;
        $upDown = 0;
        $button = [];
        if (!empty($mobileBar)) {
            $infoButton = json_decode($bar['multiple_content'], true);
            $mobileInfoButton = json_decode($mobileBar['mobile_multiple_content'], true);
            $arrayInfo = reset($mobileInfoButton);
            if ($arrayInfo['button'] != null) {
                $button = json_decode($arrayInfo['button'], true);
            }
//            $button = json_decode($arrayInfo['button'], true);
            if (!empty($button)) {
                if (array_key_exists('displayLeft', $button['button']['data']) && array_key_exists('displayRight', $button['button']['data'])) {
                    if ($button['button']['data']['displayLeft'] !== 'no check') {
                        $positionLeft = str_replace("%", "", $button['button']['data']['displayLeft']);
                    } elseif ($button['button']['data']['displayRight'] !== 'no check') {
                        $positionRight = str_replace("%", "", $button['button']['data']['displayRight']);
                    }
                }
                if (array_key_exists('upDown', $button['button']['data'])) {
                    $upDown = str_replace("px", "", $button['button']['data']['upDown']);
                }
            }
        }
        $html = '<table id="mobile-position-button" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<tr><td style="width:5%;">' .
            '<input id="mobile-check-range-left" style="margin:15px 10px 10px 15px;" type="checkbox" name="mobile-checkbox-left" value="0" disabled>' .
            '</td><td style="width:20%;">' .
            '<p style="text-align:center">Show on the left</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width:100%">' .
            '<input id="mobile-input-range-left" class="mobile-input-range-left" name="mobile-range-button-left" type="range" range="0,50" value="' . $positionLeft . '" min="0" max="50" style="display:none">' .
            '</div></td><td style="width:10%;">' .
            '<div class="range-slider">' .
            '<span id="mobile-range-value-left" style="display:none"></span>' .
            '</div></td></tr><tr><td>' .
            '<input id="mobile-check-range-right" style="margin:15px 10px 10px 15px;" type="checkbox" name="mobile-checkbox-right" value="1" disabled>' .
            '</td><td>' .
            '<p style="text-align:center">Show on the right</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width:100%">' .
            '<input id="mobile-input-range-right" class="mobile-input-range-right" name="mobile-range-button-right" type="range" range="0,50" value="' . $positionRight . '" min="0" max="50" style="display:none">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span id="mobile-range-value-right" style="display:none"></span>' .
            '</div></td></tr><tr><td>' .
            '</td><td>' .
            '<p style="text-align:center">Up-Down</p>' .
            '</td><td>' .
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input id="mobile-input-range-button-up-down" class="input-range-button-up-down" name="mobile-range-button-up-down" type="range" range="-30,30" value="' . $upDown . '" min="-30" max="30">' .
            '</div></td><td>' .
            '<div class="range-slider">' .
            '<span id="mobile-range-value-button-up-down"></span>' .
            '</div></td></tr>' .
            '</table>';
        return $html;
    }
}