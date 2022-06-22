<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 07/05/2021
 * Time: 11:08
 */

namespace Magenest\Directory\Block\System\Config\Form\Button;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Store\Model\StoreManagerInterface;

class Update extends Field
{
    protected $_template = "system/config/update.phtml";


    /**
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('directory/data/update', ['_secure' => true]);
    }


    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel = !empty($originalData['button_label']) ? $originalData['button_label'] : "Update Directory Data";
        $this->addData(
            [
                'button_label' => __($buttonLabel),
                'html_id' => $element->getHtmlId()
            ]
        );

        return $this->_toHtml();
    }
}
