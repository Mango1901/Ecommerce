<?php

namespace Magenest\PhotoReview\Block\Order\Link;

use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order;

/**
 * Class Link Review
 */
class Review extends Current
{
    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_registry = $registry;
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl($this->getPath(), ['order_id' => $this->getOrder()->getId()]);
    }

    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    private function getOrder()
    {
        return $this->_registry->registry('current_order');
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->getOrder() &&
            (
                $this->getOrder()->getStatus() !== 'complete' ||
                count($this->getOrder()->getAllItems()) == 1
            )
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
