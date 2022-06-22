<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\PaymentEPay\Api\Data;

/**
 * @api
 * @since 100.0.2
 */
interface PaymentAttributeInterface
{
    const CODE_VNPT_EPAY = 'vnpt_epay';
    const PAY_CREATE_TOKEN = 'PAY_AND_CREATE_TOKEN';
    const PAY_RETURNED_TOKEN = 'PAY_WITH_RETURNED_TOKEN';
    const PAY_WITH_TOKEN     = 'PAY_WITH_TOKEN';
    const PURCHASE_OTP       = 'PURCHASE_OTP';
    const IS_TEST = 'payment/vnpt_epay/is_test';
    const TEST_MER_ID = 'payment/vnpt_epay/test_merchant_id';
    const TEST_ENCODE_KEY = 'payment/vnpt_epay/test_encode_key';
    const TEST_CANCEL_PASSWORD = 'payment/vnpt_epay/test_cancel_password';
	const TEST_DOMAIN = 'payment/vnpt_epay/test_domain_url';
    const TEST_CHECKTRANS_URL = 'payment/vnpt_epay/test_checktrans_url';
    const TEST_CACELTRANS_URL = 'payment/vnpt_epay/test_canceltrans_url';
    const TEST_KEY3DES_ENCRYPT = 'payment/vnpt_epay/test_key3ds_encrypt';
    const TEST_KEY3DES_DECRYPT = 'payment/vnpt_epay/test_key3ds_decrypt';
    const MER_ID = 'payment/vnpt_epay/merchant_id';
    const ENCODE_KEY = 'payment/vnpt_epay/encode_key';
    const CANCEL_PASSWORD = 'payment/vnpt_epay/cancel_password';
	const DOMAIN = 'payment/vnpt_epay/domain_url';
    const CHECKTRANS_URL = 'payment/vnpt_epay/checktrans_url';
    const CACELTRANS_URL = 'payment/vnpt_epay/canceltrans_url';
    const KEY3DES_ENCRYPT = 'payment/vnpt_epay/key3ds_encrypt';
    const KEY3DES_DECRYPT = 'payment/vnpt_epay/key3ds_decrypt';
    const TEST_PARTNER_CODE = 'payment/vnpt_epay/test_partner_code';
    const TEST_DISBURSEMENT_URL = 'payment/vnpt_epay/test_disbursement_url';
    const PARTNER_CODE = 'payment/vnpt_epay/partner_code';
    const DISBURSEMENT_URL = 'payment/vnpt_epay/disbursement_url';
    const STATUS_REFUND = '2';
    const IS_VISIBLE_TOKEN = '1';
    const IS_ACTIVE_TOKEN = '1';
    const PAY_WITH_NO_OPTION = "NO";
    const QR_PAYMENT_IS_TEST = 'payment/vnpt_epay_qrcode/is_test';
    const QR_PAYMENT_TEST_MER_ID = 'payment/vnpt_epay_qrcode/test_merchant_id';
    const QR_PAYMENT_MER_ID = 'payment/vnpt_epay_qrcode/merchant_id';
    const QR_PAYMENT_TEST_DOMAIN = 'payment/vnpt_epay_qrcode/test_domain_url';
    const QR_PAYMENT_DOMAIN = 'payment/vnpt_epay_qrcode/domain_url';
    const QR_PAYMENT_TEST_ENCODE_KEY = 'payment/vnpt_epay_qrcode/test_encode_key';
    const QR_PAYMENT_ENCODE_KEY = 'payment/vnpt_epay_qrcode/encode_key';
    const IS_PAYMENT_IS_TEST = 'payment/vnpt_epay_is/is_test';
    const IS_PAYMENT_TEST_MER_ID = 'payment/vnpt_epay_is/test_merchant_id';
    const IS_PAYMENT_MER_ID = 'payment/vnpt_epay_is/merchant_id';
    const IS_PAYMENT_TEST_DOMAIN = 'payment/vnpt_epay_is/test_domain_url';
    const IS_PAYMENT_DOMAIN = 'payment/vnpt_epay_is/domain_url';
    const IS_PAYMENT_TEST_ENCODE_KEY = 'payment/vnpt_epay_is/test_encode_key';
    const IS_PAYMENT_ENCODE_KEY = 'payment/vnpt_epay_is/encode_key';
    const IS_TEST_CHECKTRANS_URL = 'payment/vnpt_epay_is/test_canceltrans_url';
    const IS_CHECKTRANS_URL = 'payment/vnpt_epay_is/canceltrans_url';
    const IS_PAYMENT_TEST_LISTING_URL = 'payment/vnpt_epay_is/test_installment_bank_listing_url';
    const IS_PAYMENT_LISTING_URL = 'payment/vnpt_epay_is/test_installment_bank_listing_url';
    const IS_TEST_CANCEL_PASSWORD = 'payment/vnpt_epay_is/test_cancel_password';
    const IS_CANCEL_PASSWORD = 'payment/vnpt_epay_is/cancel_password';
}
