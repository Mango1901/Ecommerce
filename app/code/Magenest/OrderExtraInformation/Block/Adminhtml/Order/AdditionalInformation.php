<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderExtraInformation\Block\Adminhtml\Order;

use Magento\Framework\Registry;
use Magento\Payment\Helper\Data as PaymentHelper;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;
use Magento\Framework\View\Element\Template\Context as TemplateContext;

class AdditionalInformation extends \Magento\Sales\Block\Order\Info
{
    const EDIT_INFORMATION_RESOURCE = "Magenest_OrderExtraInformation::update_additional_info";

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * AdditionalInformation constructor.
     *
     * @param TemplateContext $context
     * @param Registry $registry
     * @param PaymentHelper $paymentHelper
     * @param AddressRenderer $addressRenderer
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        TemplateContext $context,
        Registry $registry,
        PaymentHelper $paymentHelper,
        AddressRenderer $addressRenderer,
        \Magento\Framework\AuthorizationInterface $authorization,
        array $data = []
    ) {
        $this->_authorization = $authorization;
        parent::__construct($context, $registry, $paymentHelper, $addressRenderer, $data);
    }

    public function isAllowEditAction()
    {
        return $this->_authorization->isAllowed(self::EDIT_INFORMATION_RESOURCE);
    }
}
