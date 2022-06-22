<?php
namespace Magenest\ProductLabel\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ToggleButton
 * @package Magenest\ProductLabel\Block\Adminhtml
 */
class ToggleButton extends Field
{
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $block = $this->_layout->addBlock(
            \Magenest\ProductLabel\Block\Adminhtml\ToggleButton::class
        );

        return $block->setElement($element)->toHtml();
    }

}
