<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 21/06/2022
 * Time: 09:09
 */
declare(strict_types=1);

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Api\GetReferralCodeByCustomerInterface;
use Magenest\RewardPoints\Helper\Data;

/**
 * Class GetReferralCodeByCustomer
 */
class GetReferralCodeByCustomer implements GetReferralCodeByCustomerInterface
{
    /**
     * @var ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var Data
     */
    protected $_helper;

    public function __construct(
        ReferralFactory $referralFactory,
        Data $helper
    ) {
        $this->referralFactory = $referralFactory;
        $this->_helper = $helper;
    }

    /**
     * @inheritDoc
     */
    public function execute($customerId)
    {
        $referralCode = $this->referralFactory->create()->load($customerId, 'customer_id');
        $code = $referralCode->getData('referral_code');
        if (empty($code)) {
            $code = $this->createCode();
            $referral = $this->referralFactory->create();
            $referral->setData([
                'customer_id' => $customerId,
                'referral_code' => $code
            ]);
            $referral->save();
        }
        return $code;
    }

    /**
     * Generate code
     *
     * @return mixed
     */
    public function createCode()
    {
        $gen_arr = [];

        $pattern = $this->_helper->getCodePattern();
        if (!$pattern) {
            $pattern = '[A1][N1][A1][N1][A1][N1]';
        }

        preg_match_all("/\[[AN][.*\d]*\]/", $pattern, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $delegate = substr($match [0], 1, 1);
            $length = substr($match [0], 2, strlen($match [0]) - 3);
            $gen = '';
            if ($delegate == 'A') {
                $gen = $this->generateString($length);
            } elseif ($delegate == 'N') {
                $gen = $this->generateNum($length);
            }

            $gen_arr [] = $gen;
        }
        foreach ($gen_arr as $g) {
            $pattern = preg_replace('/\[[AN][.*\d]*\]/', $g, $pattern, 1);
        }

        return $pattern;
    }

    /**
     * Generate String
     *
     * @param $length
     * @return string
     */
    private function generateString($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $string[rand(0, 51)];
        }

        return $rand;
    }

    /**
     * Generate Number
     *
     * @param $length
     * @return string
     */
    private function generateNum($length)
    {
        if ($length == 0 || $length == null || $length == '') {
            $length = 5;
        }
        $number = "0123456789";
        $rand = '';
        for ($i = 0; $i < $length; $i++) {
            $rand .= $number[rand(0, 9)];
        }

        return $rand;
    }
}
