<?php

namespace Magenest\OrderCancel\Api;

/**
 * Service send sms of lac hong when Cancellation order
 */
interface SendSmsCancelOrderInterface
{
    /**
     * Path of webservice
     */
    const WSDL_LAC_HONG = 'http://www.vas.lachongmedia.vn/smsbrandname/Service.asmx?WSDL';
    const BRANCH_NAME = 'brandname';
    const USERNAME = 'username';
    const PASSWORD = 'password';
    const MESSAGE = 'message';
    const PHONE = 'phone';

    /**
     * Response code
     */
    const RESPONSE_CODE = [
        '200' => 'Success',
        '201' => 'Sending failed',
        '202' => 'Incorrect account',
        '203' => 'Invalid or no phone number support',
        '204' => 'Invalid account',
        '205' => 'Not enough MT to send messages',
        '206' => 'Wrong BlockId',
        '207' => 'SMSId already exists',
        '-1' => 'Error when open block'
    ];

    /**
     * @param string $message
     * @param string $phone
     * @return bool
     */
    public function sendSingleSms($message, $phone);
}
