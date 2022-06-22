<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Model\UrlInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Backend\Block\Widget\Button;

/**
 * Class Export
 * @package Magenest\CustomTableRate\Block\Adminhtml\Form\Field
 */
class Export extends AbstractElement
{
    /**
     * @var UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var string
     */
    protected $_method = '';

    /**
     * Constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param UrlInterface $backendUrl
     * @param string $method
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        UrlInterface $backendUrl,
        $method = '',
        array $data = []
    )
    {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->_backendUrl = $backendUrl;
        $this->_method = $method;
    }

    /**
     * {@inheritdoc}
     */
    public function getElementHtml()
    {
        /** @var \Magento\Backend\Block\Widget\Button $buttonBlock */
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock(Button::class);
        $url = $this->_backendUrl->getUrl("*/*/export", ['website' => $buttonBlock->getRequest()->getParam('website'), 'method' => $this->_method]);

        return $buttonBlock->setData([
            'label' => __('Export CSV'),
            'onclick' => "setLocation('{$url}')",
            'class' => '',
        ])->toHtml();
    }
}
