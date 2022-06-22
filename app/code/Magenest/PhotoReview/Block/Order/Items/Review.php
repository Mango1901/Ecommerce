<?php

namespace Magenest\PhotoReview\Block\Order\Items;

use Magento\Framework\App\Http\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Payment\Helper\Data;
use Magento\Sales\Model\Order;

/**
 * Class Items Review
 */
class Review extends Template
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Context
     * @since 101.0.0
     */
    protected $httpContext;

    /**
     * @var Data
     */
    protected $_paymentHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param Registry $registry
     * @param Context $httpContext
     * @param Data $paymentHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Registry $registry,
        Context $httpContext,
        Data $paymentHelper,
        array $data = []
    ) {
        $this->_paymentHelper = $paymentHelper;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Retrieve current order model instance
     *
     * @return Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
}
