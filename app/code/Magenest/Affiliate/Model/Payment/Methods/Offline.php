<?php


namespace Magenest\Affiliate\Model\Payment\Methods;

use Magenest\Affiliate\Model\Payment\Methods;

/**
 * Class Offline
 * @package Magenest\Affiliate\Model\Payment\Methods
 */
class Offline extends Methods
{
    /**
     * @inheritdoc
     */
    public function getMethodDetail()
    {
        return [
            'offline_address' => [
                'type' => 'textarea',
                'label' => __('Address'),
                'name' => 'offline_address'
            ]
        ];
    }
}
