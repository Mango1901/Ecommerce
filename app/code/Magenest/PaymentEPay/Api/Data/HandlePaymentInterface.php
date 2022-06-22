<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\PaymentEPay\Api\Data;

/**
 * Manages locale config information
 *
 * @api
 * @since 100.0.2
 */
interface HandlePaymentInterface
{
    /**
     * @param $orderIncrementId
     * @param $payOption
     * @return array
     */
    public function handlePaymentNotToken($orderIncrementId, $payOption): array;

    /**
     * @param $orderIncrementId
     * @param $payOption
     * @return array
     */
    public function handleQRCodePayment($orderIncrementId, $payOption): array;

    /**
     * @param $orderIncrementId
     * @param $payOption
     * @return array
     */
    public function handleInstallmentPayment($orderIncrementId, $payOption): array;

    /**
     * @param $amount
     * @return array
     */
    public function handleInstallmentPaymentListing($amount): array;

    /**
     * @param $orderIncrementId
     * @param $payOption
     * @param $payType
     * @return array
     */
    public function handlePaymentWithToken($orderIncrementId, $payOption , $payType): array;

    /**
     * @param $trxId
     * @param $refundAmount
     * @param $payType
     * @param $cancelMsg
     * @return mixed
     */
    public function handleCancelTrans($trxId, $refundAmount, $payType, $cancelMsg);

    /**
     * @param $trxId
     * @param $payType
     * @param string $type
     * @param $cancelMsg
     * @return array
     */
    public function handleCancelTransIS($trxId, $payType, $type, $cancelMsg);

    /**
     * @param string $orderIncrementId
     * @return mixed
     */
    public function handlePaymentWithNoOption(string $orderIncrementId);
}
