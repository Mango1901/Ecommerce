<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Block\Adminhtml\Template;

class Main extends \Magento\Framework\View\Element\Template
{
    protected $_labelCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magenest\MegaMenu\Model\ResourceModel\Label\CollectionFactory $_labelCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_labelCollectionFactory = $_labelCollectionFactory;
    }

    public function getLabelArr()
    {
        /** @var \Magenest\MegaMenu\Model\ResourceModel\Label\Collection $labelCollection */
        $labelCollection = $this->_labelCollectionFactory->create();
        $return = $labelCollection->toOptionArray();
        array_unshift($return, [
            'label' => __("Select Label"),
            'value' => 0
        ]);

        return $return;
    }
}
