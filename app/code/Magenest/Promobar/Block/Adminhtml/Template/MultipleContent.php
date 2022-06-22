<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class MultipleContent  extends \Magento\Framework\Data\Form\Element\AbstractElement
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
        if(isset($bar['multiple_content'])) {
            $dataPromobar = $bar['multiple_content'];
        }else{
            $dataPromobar = json_encode([]);
        }

        if(isset($mobileBar['mobile_multiple_content'])) {
            $mobileDataPromobar = $mobileBar['mobile_multiple_content'];

            $bar['multiple_content']              = json_decode($bar['multiple_content'], true);
            $mobileBar['mobile_multiple_content'] = json_decode($mobileBar['mobile_multiple_content'], true);

            $bar['multiple_content'][0]['mobile_content'] = $mobileBar['mobile_multiple_content'][0]['content'];
            $bar['multiple_content'][0]['mobile_button']  = $mobileBar['mobile_multiple_content'][0]['button'];

            $bar['multiple_content'] = json_encode($bar['multiple_content']);

            $dataPromobar = $bar['multiple_content'];
        }else{
            $mobileDataPromobar = json_encode([]);
        }
        $multipleContent = $this->getValue();
        $html = '<div name="multiple_content" id="mapmultiplecontent" data-bind="scope:\'mapmultiplecontent\'">'.
            '<input name="multiple_content" id="multiple_content" value type="text" style="display: none"/>'.
            "<!-- ko template: 'Magenest_Promobar/options' --><!-- /ko -->".
            '</div>';
        $html .=    '<script type="text/x-magento-init">
        {
            "*": {
                "Magento_Ui/js/core/app": {
                    "components": {
                        "mapmultiplecontent": {
                            "component": "Magenest_Promobar/js/multiplecontent",
                            "template" : "Magenest_Promobar/options",
                            "config" : {
                                "mapmultiplecontent": '.$dataPromobar.',
                                "mapmobilemultiplecontent": '.$mobileDataPromobar.'
                            }
                        }
                    }
                }
            }
        }
</script>';

        return $html;
    }
}