<?php

namespace Magenest\Affiliate\Model\Config\Backend;

use Magenest\PaymentEPay\Api\Data\PaymentAttributeInterface;
use Magento\Config\Model\Config\Backend\Serialized;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Phrase;

/**
 * Validate of fee payment method withdraw
 */
class FeeOfWithdraw extends Serialized
{
    const FEE_REGEX_PATTERN = '/^(((0|[1-9]\d?)(\.\d{1,2})?|100(\.00?)?)(\%)$)|^(?:[1-9]\d*|0)?(?:\.\d+)?$/';
    const FEE = 'fee';

    /**
     * @inheritDoc
     */
    public function beforeSave()
    {
        if ($data = $this->getValue()) {
            $fee = $data[PaymentAttributeInterface::CODE_VNPT_EPAY][self::FEE] ?? "";
            if (!preg_match(self::FEE_REGEX_PATTERN, $fee)) {
                throw new InputException(new Phrase(__('Please enter valid fee format')));
            }
        }

        return parent::beforeSave();
    }
}
