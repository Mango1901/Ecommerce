<?php


namespace Magenest\Affiliate\Model\Withdraw;

use Magento\Framework\Option\ArrayInterface;
use Magenest\Affiliate\Helper\Payment;
use Zend_Serializer_Exception;

/**
 * Class Method
 * @package Magenest\Affiliate\Model\Withdraw
 */
class Method implements ArrayInterface
{
    /**
     * @var Payment
     */
    protected $_paymentHelper;

    /**
     * Method constructor.
     *
     * @param Payment $helper
     */
    public function __construct(Payment $helper)
    {
        $this->_paymentHelper = $helper;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        foreach ($this->getOptionHash() as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $options;
    }

    /**
     * @return array
     * @throws Zend_Serializer_Exception
     */
    public function getOptionHash()
    {
        $options = [];
        $paymentMethods = $this->_paymentHelper->getActiveMethods();
        foreach ($paymentMethods as $key => $method) {
            $options[$key] = $method['label'];
        }

        return $options;
    }
}
