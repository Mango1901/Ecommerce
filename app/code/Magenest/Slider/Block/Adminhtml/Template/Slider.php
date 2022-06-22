<?php

namespace Magenest\Slider\Block\Adminhtml\Template;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Menu
 * @package Magenest\Slider\Block\Adminhtml\Template
 */
class Slider extends AbstractElement
{
    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\LayoutInterface $layout,
        array $data = []
    ) {
        $this->_layout = $layout;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getElementHtml()
    {
        return $this->_layout->createBlock(Main::class)
            ->setTemplate('Magenest_Slider::slider/main.phtml')
            ->toHtml();
    }
}
