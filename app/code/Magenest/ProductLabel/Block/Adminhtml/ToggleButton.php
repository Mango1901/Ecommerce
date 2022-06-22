<?php
namespace Magenest\ProductLabel\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

/**
 * Class ToggleButton
 * @package Magenest\ProductLabel\Block\Adminhtml
 */
class ToggleButton extends Template
{
    /** @var string  */
    protected $_template = 'Magenest_ProductLabel::config/toggleButton.phtml';

    /**
     * @return int
     */
    public function getValue()
    {
        $value = $this->getElement()->getValue();
        return $value != null ? $value : 0;
    }
}
