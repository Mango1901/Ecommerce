<?php

namespace Magenest\Affiliate\Block\Adminhtml\Customer\Field;

use Magenest\Affiliate\Helper\Data as HelperData;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

/**
 * Balance column renderer
 * @api
 * @since 100.0.2
 */
class Balance extends AbstractRenderer
{
    /**
     * @var HelperData
     */
    protected $helperData;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param HelperData $helperData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        HelperData $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct($context, $data);
    }

    /**
     * Renders a column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());
        return $this->helperData->formatPrice($value);
    }
}
