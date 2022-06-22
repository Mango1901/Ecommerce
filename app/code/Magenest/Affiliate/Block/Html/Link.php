<?php


namespace Magenest\Affiliate\Block\Html;

use Magento\Framework\View\Element\Template\Context;
use Magenest\Affiliate\Helper\Data;

/**
 * Class Link
 * @package Magenest\Affiliate\Block\Html
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Link constructor.
     *
     * @param Context $context
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        $type = $this->getType();
        if (strpos($this->helper->showAffiliateLinkOn(), $type) !== false) {
            return parent::_toHtml();
        }

        return '';
    }
}
