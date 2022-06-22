<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\PaymentEPay\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

/**
 * Class Info
 *
 * @deprecated Starting from Magento 2.3.6 Braintree payment method core integration is deprecated
 * in favor of official payment integration available on the marketplace
 */
class Info extends ConfigurableInfo
{
    /**
     * Returns label
     *
     * @param string $field
     */
    protected function getLabel($field)
    {
        $fields = [
            'merId' => "Merchant Website Id",
            'userId' => "Customer Id",
            'cardNo' => "Card Number",
            'invoiceNo' => "Order Increment Id",
            'resultMsg' => "Response Msg",
            'payType' => "Payment Type",
            'trxId' => "Transaction No",
            'bankId' => 'Bank Code',
            'amountIS' => 'Amount IS'
        ];
        return $fields[$field];
    }

    /**
     * Custom Value View Of Payment Information Epay
     *
     * @param string $field
     * @param string $value
     * @return Phrase|string
     */
    protected function getValueView($field, $value)
    {
        if ($field === 'payType') {
            if ($value == 'DC') {
                return __('Local Debit Card (ATM bank card)');
            } else if ($value == 'IC') {
                return __('International Credit/Debit Card');
            } else if ($value == 'EW') {
                return __('E-Wallet');
            } else if ($value == 'IS') {
                return __('Installment Payment');
            }
        }
        return parent::getValueView($field, $value);
    }
}
