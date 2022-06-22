<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Promobar\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;

class CreateWidget extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    protected $_elements;

    protected $registry;

    protected $urlBuilder;

    public function __construct(Factory $factoryElement,
                                CollectionFactory $factoryCollection,
                                Escaper $escaper,
                                \Magento\Framework\Registry $registry,
                                \Magento\Framework\UrlInterface $urlBuilder,
                                array $data = [])
    {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    public function getElementHtml()
    {
        $html = "<button type='button' id='createwidget' class='createwidget'><span>Create Widget</span></button>";
        $html .= "<input type='hidden' id='widget_value' name='widget_value' value='0'/>";
        $html .=   "<script>
                    require(['jquery'],
                        function($) {
                                $(document).on('click', '.createwidget', function(event){
                                    $('input[name=\"widget_value\"]').val(1);
                                    $( \"#edit_form\" ).trigger( \"submit\" );
                                })
                            });
            </script>";
        return $html;
    }


}