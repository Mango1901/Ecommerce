<?php


namespace Magenest\PaymentEPay\Model;


use Magenest\PaymentEPay\Api\Data\HandlePaymentInterface;
use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;

class PrepareOrderRequest
{
    /**
     * @var HandlePaymentInterface
     */
    protected $handlePayment;

    public function __construct(HandlePaymentInterface $handlePayment)
    {
        $this->handlePayment = $handlePayment;
    }


    /**
     * @param $payOption
     * @param $payType
     * @param $orderIncrementId
     * @return array
     */
    public function execute($orderIncrementId, $payType = null, $payOption = null): array
    {
        $result = [];
        if ($payType == PaymentAttributeInterface::PAY_WITH_NO_OPTION){
            $result = $this->handlePayment->handlePaymentWithNoOption($orderIncrementId);
        }else{
            if($payOption == PaymentAttributeInterface::PAY_CREATE_TOKEN
                || $payOption == PaymentAttributeInterface::PAY_RETURNED_TOKEN
                || $payOption == ''){
                $result = $this->handlePayment->handlePaymentNotToken($orderIncrementId, $payOption);
            }
            if($payOption == PaymentAttributeInterface::PAY_WITH_TOKEN
                || $payOption == PaymentAttributeInterface::PURCHASE_OTP){
                $result = $this->handlePayment->handlePaymentWithToken($orderIncrementId, $payOption, $payType);
            }
        }
        return $result;
    }
}
