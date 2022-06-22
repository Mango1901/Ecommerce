<?php


namespace Magenest\Affiliate\Model\Payment;

use Magento\Framework\DataObject;
use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class Methods
 * @package Magenest\Affiliate\Model\Payment
 */
class Methods extends DataObject
{
    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * Methods constructor.
     *
     * @param JsonHelper $helper
     * @param array $data
     */
    public function __construct(JsonHelper $helper, array $data = [])
    {
        $this->_jsonHelper = $helper;

        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getWithdrawInfoDetail()
    {
        $detail = [];
        $withdraw = $this->getData('withdraw');
        foreach ($this->getMethodDetail() as $key => $value) {
            $detail[$key] = $withdraw->getData($key);
        }

        return $this->_jsonHelper->jsonEncode($detail);
    }

    /**
     * @return array
     */
    public function getPaymentDetail()
    {
        $detail = [];
        $withdraw = $this->getData('withdraw');
        if (!$withdraw->getPaymentDetails()) {
            return $detail;
        }

        $paymentDetail = $this->_jsonHelper->jsonDecode($withdraw->getPaymentDetails());
        foreach ($this->getMethodDetail() as $key => $value) {
            if (isset($paymentDetail[$key])) {
                $detail[$key] = [
                    'label' => $value['label'],
                    'value' => $paymentDetail[$key]
                ];
            }
        }

        return $detail;
    }
}
