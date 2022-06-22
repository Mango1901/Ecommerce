<?php


namespace Magenest\Affiliate\Model\Payment\Methods;

use Magenest\Affiliate\Model\Payment\Methods;

/**
 * Class Paypal
 * @package Magenest\Affiliate\Model\Payment\Methods
 */
class Paypal extends Methods
{
    /**
     * @inheritdoc
     */
    public function getMethodDetail()
    {
        return [
            'paypal_email' => [
                'type' => 'text',
                'label' => __('Paypal Email'),
                'name' => 'paypal_email',
                'required' => true,
                'class' => 'required-entry validate-email'
            ],
            'paypal_transaction_id' => [
                'type' => 'text',
                'label' => __('Paypal transaction Id'),
                'name' => 'paypal_transaction_id'
            ]
        ];
    }
}
