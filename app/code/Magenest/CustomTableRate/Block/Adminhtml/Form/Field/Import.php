<?php

namespace Magenest\CustomTableRate\Block\Adminhtml\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Import
 * @package Magenest\CustomTableRate\Block\Adminhtml\Form\Field
 */
class Import extends AbstractElement
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setType('file');
    }
}
