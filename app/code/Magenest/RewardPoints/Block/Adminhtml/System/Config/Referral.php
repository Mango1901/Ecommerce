<?php
namespace Magenest\RewardPoints\Block\Adminhtml\System\Config;

class Referral extends \Magento\Config\Block\System\Config\Form\Field {
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $values = $element->getValues();
        $html = '<select class="points-person-earner" style="list-style: none">';
        $isSelect = true;
        if($values){
            foreach ($values as $dat) {
                if($dat['value'] != "-1"){
                    $html .= "<option value='{$dat['value']}'>{$dat['label']}</option>";
                }else{
                    $html = "<span style='color: red;'>{$dat['label']}</span>";
                    $isSelect = false;
                }
            }
        }
        if($isSelect){
            $html .= "</select>";
        }
        return $html;
    }
}