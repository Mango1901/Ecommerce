<?php

namespace Magenest\RewardPoints\Setup;

/**
 * Class RewardPointSetup
 * @package Magenest\RewardPoints\Setup
 */
class RewardPointSetup extends \Magento\Quote\Setup\QuoteSetup
{
    /**
     * List of entities converted from EAV to flat data structure
     *
     * @var $_flatEntityTables array
     */
    protected $_flatEntityTables = [
        'quote' => 'quote',
        'quote_item' => 'quote_item',
        'quote_address' => 'quote_address',
        'quote_address_item' => 'quote_address_item',
        'quote_address_rate' => 'quote_shipping_rate',
        'quote_payment' => 'quote_payment',
        'order'=>'sales_order',
        'invoice'=>'sales_invoice',
        'creditmemo'=>'sales_creditmemo'
    ];
}
