<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class ChangeBorderWidth  extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        $button = $this->registry->registry('promobar_buttons')->getData();
        if(!empty($button)){
            $border_width = $button['border_width'];
        }else{
            $border_width = '1';
        }
        $borderWidth = $this->getValue();
        $html = '<table id="edit-height" border="0" cellspacing="3" cellpadding="0">';
        $html .= '<tr><td>'.
            '<div class="range-slider form-inline-edit" style="width: 100%">' .
            '<input name="border_width" class="input-range-border-width" range="1,10" type="range" value="'.$border_width.'" min="1" max="10">' .
            '</div></td><td style="width:10%;">' .
            '<div class="range-slider">'.
            '<span class="range-value-border-width"></span>' .
            '</div></td></tr></table>';
        return $html;
    }
}