<?php


namespace Magenest\Affiliate\Model\Total\Quote;

use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magenest\Affiliate\Helper\Calculation\Commission;
use Magenest\Affiliate\Helper\Calculation\Discount;
use Magenest\Affiliate\Helper\Data;
use Zend_Serializer_Exception;

/**
 * Class Affiliate
 * @package Magenest\Affiliate\Model\Total\Quote
 */
class Affiliate extends AbstractTotal
{
    /**
     * Multi shipping
     *
     * @var string
     */
    const MULTI_SHIPPING = 'multishipping';
    /**
     * @var Discount
     */
    protected $_discountHelper;

    /**
     * @var Commission
     */
    protected $_commissionHelper;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * @var Http
     */
    protected $_request;

    /**
     * Affiliate constructor.
     *
     * @param Discount $discountHelper
     * @param Commission $commissionHelper
     * @param Data $dataHelper
     * @param Http $request
     */
    public function __construct(
        Discount $discountHelper,
        Commission $commissionHelper,
        Data $dataHelper,
        Http $request
    ) {
        $this->_discountHelper = $discountHelper;
        $this->_commissionHelper = $commissionHelper;
        $this->_dataHelper = $dataHelper;
        $this->_request = $request;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     *
     * @return $this
     * @throws LocalizedException *@throws \Zend_Serializer_Exception
     * @throws Zend_Serializer_Exception
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        if ($this->isMultiShipping()) {
            return $this;
        }

        if (($quote->isVirtual() && ($this->_address->getAddressType() === 'shipping')) ||
            (!$quote->isVirtual() && ($this->_address->getAddressType() === 'billing'))
        ) {
            return $this;
        }

        $this->_discountHelper->collect($quote, $shippingAssignment, $total);
        $this->_commissionHelper->collect($quote, $shippingAssignment, $total);

        return $this;
    }

    /**
     * @param Quote $quote
     * @param Total $total
     *
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {
        $result = [];
        $amount = $quote->getAffiliateDiscountAmount();
        if ($amount > 0.001 && !$this->isMultiShipping()) {
            $result[] = [
                'code' => $this->getCode(),
                'title' => __('Affiliate Discount'),
                'value' => -$amount
            ];
        }

        return $result;
    }

    /**
     * @return bool
     */
    private function isMultiShipping()
    {
        return $this->_request->getRouteName() === self::MULTI_SHIPPING;
    }
}
