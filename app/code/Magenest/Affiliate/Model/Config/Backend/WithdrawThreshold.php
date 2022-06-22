<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\Affiliate\Model\Config\Backend;

use Exception;
use Magenest\Affiliate\Helper\Payment;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

/**
 * @api
 * @since 100.0.2
 */
class WithdrawThreshold extends Value
{
    private $paymentHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Payment $paymentHelper,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->paymentHelper = $paymentHelper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    /**
     * Validate a safe withdraw threshold  field value
     *
     * @return void
     * @throws LocalizedException
     * @throws Exception
     */
    public function beforeSave()
    {
        $valueThreshold = $this->getValue();
        $maximum = $this->getFieldsetDataValue('maximum');
        $minimum = $this->getFieldsetDataValue('minimum');
        $fee = $this->getFieldsetDataValue('payment_method')['vnpt_epay']['fee'] ?? 0;

        $satisfactionAmount = $this->satisfactionMinimumAmountIncludeFee($minimum, $fee);

        if (!$satisfactionAmount['status']) {
            $minimumRequire = $this->paymentHelper->formatPrice($satisfactionAmount['amount_satisfaction']);
            throw new LocalizedException(new Phrase(__('Minimum withdraw amount must greater than %1', $minimumRequire)));
        }

        if ($maximum < 10000 || $minimum < 10000 || $valueThreshold < 10000) {
            throw new LocalizedException(new Phrase(__('Minimum withdraw amount must greater than 10000')));
        }

        if ($maximum && $valueThreshold > $maximum) {
            throw new LocalizedException(new Phrase(__('Safe withdraw threshold amount must less than maximum withdraw amount')));
        }

        if ($minimum && $valueThreshold < $minimum) {
            throw new LocalizedException(new Phrase(__('Safe withdraw threshold amount must greater than minimum withdraw amount')));
        }
    }

    /**
     * @param string $minimum
     * @param string $fee
     * @return array
     */
    private function satisfactionMinimumAmountIncludeFee($minimum, $fee)
    {
        if (strpos($fee, '%') !== false) {
            $feeConfig = floatval(trim($fee, '%'));
            if (($minimum - $minimum * $feeConfig / 100) < 10000) {
                return [
                    'status' => false,
                    'amount_satisfaction' => 10000 / (1 - $feeConfig / 100)
                ];
            }
        } else {

            $feeConfig = floatval($fee);
            if (($minimum - $feeConfig) < 10000) {
                return [
                    'status' => false,
                    'amount_satisfaction' => 10000 + $feeConfig
                ];
            }
        }
        return [
            'status' => true
        ];
    }
}
