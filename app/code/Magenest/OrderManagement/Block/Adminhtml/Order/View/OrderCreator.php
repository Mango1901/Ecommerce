<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */

namespace Magenest\OrderManagement\Block\Adminhtml\Order\View;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\View\Tabs;

class OrderCreator extends Tabs
{
    /**
     * @var \Magenest\OrderManagement\Model\Order
     */
    private $om;
    /**
     * @var \Magenest\OrderManagement\Model\Config\SourceOption
     */
    private $sourceOption;

    /**
     * OrderCreator constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Registry $registry
     * @param \Magenest\OrderManagement\Model\Order $om
     * @param \Magenest\OrderManagement\Model\Config\SourceOption $sourceOption
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        \Magenest\OrderManagement\Model\Order $om,
        \Magenest\OrderManagement\Model\Config\SourceOption $sourceOption,
        array $data = []
    ) {
        $this->om = $om;
        parent::__construct($context, $jsonEncoder, $authSession, $registry, $data);
        $this->sourceOption = $sourceOption;
    }

    /**
     * @throws LocalizedException
     */
    public function getOrderCreator()
    {
        return $this->getOrder()->getData('order_creator');
    }

    /**
     * @throws LocalizedException
     */
    public function getWarehouse()
    {
        return $this->getOrder()->getData('warehouse');
    }

    public function _toHtml()
    {
        $html  = '';
        if (!$this->om->canConfirm($this->getOrder())) {
            $html = parent::_toHtml();
        }
        return $html;
    }

    public function getSourceWarehouse()
    {
        return $this->sourceOption->getOptionArray();
    }

    public function getWarehouseText($warehouse)
    {
        return $this->sourceOption->getTextByOptionId($warehouse);
    }
}
