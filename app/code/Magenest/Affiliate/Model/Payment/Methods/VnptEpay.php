<?php


namespace Magenest\Affiliate\Model\Payment\Methods;

use Magenest\Affiliate\Model\Payment\Methods;

/**
 * Class Banktranfer
 * @package Magenest\Affiliate\Model\Payment\Methods
 */
class VnptEpay extends Methods
{
    /**
     * @inheritdoc
     */
    public function getMethodDetail()
    {
        return [
            'vnpt_epay' .
            '' => [
                'type' => 'textarea',
                'label' => __('VNPT E-Pay'),
                'name' => 'VNPT E-Pay'
            ]
        ];
    }
}
