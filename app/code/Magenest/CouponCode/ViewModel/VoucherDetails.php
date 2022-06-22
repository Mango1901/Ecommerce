<?php

namespace Magenest\CouponCode\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\RuleFactory;
use Magento\Shipping\Model\CarrierFactory;
use Psr\Log\LoggerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\SalesRule\Model\Rule\Condition\Address;

/**
 * Class VoucherDetails
 */
class VoucherDetails implements ArgumentInterface
{
    const REGEX_CONDITION_PRODUCT = '/(sku|category_ids|quote_item_row_total|quote_item_qty|quote_item_price)/';

    /**
     * @var Rule|null
     */
    private $ruleCache = null;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Json
     */
    private $json;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var RuleFactory
     */
    private $ruleFactory;

    /**
     * @var Address
     */
    private $addressCondition;

    /**
     * @var CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        ResourceConnection $resourceConnection,
        LoggerInterface $logger,
        Json $json,
        PriceCurrencyInterface $priceCurrency,
        RuleFactory $ruleFactory,
        Address $addressCondition,
        CarrierFactory $carrierFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->logger = $logger;
        $this->json = $json;
        $this->priceCurrency = $priceCurrency;
        $this->ruleFactory = $ruleFactory;
        $this->addressCondition = $addressCondition;
        $this->_carrierFactory = $carrierFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * @param $couponCode
     * @param $ruleId
     * @return array
     */
    public function getVoucherByCouponCodeAndRuleId($couponCode, $ruleId)
    {
        try {
            $connection = $this->resourceConnection->getConnection();
            $salesRuleTable = $connection->getTableName('salesrule');
            $salesRuleCouponTable = $connection->getTableName('salesrule_coupon');
            $select = $connection->select()->from(['sr' => $salesRuleTable])
                ->join(['src' => $salesRuleCouponTable], 'src.rule_id = sr.rule_id')
                ->where('src.rule_id = ?', $ruleId)
                ->where('src.code = ?', $couponCode);
            return $connection->fetchRow($select);
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return [];
    }

    /**
     * Unserialize Data
     *
     * @param string $data
     * @return array|bool|float|int|mixed|string|null
     */
    public function handleImage($data)
    {
        $img = $this->json->unserialize($data);
        if (isset($img[0]['url'])) {
            return $img[0]['url'];
        }
        return null;
    }

    /**
     * @param $actionType
     * @param $discountAmount
     * @return string
     */
    public function getDiscountOfVoucher($actionType, $discountAmount)
    {
        $prefixDiscount = __('Discount Amount');
        if ($actionType == 'by_percent') {
            return $prefixDiscount . ' ' . round($discountAmount) . '%';
        } else {
            return $prefixDiscount. $this->priceCurrency->format($discountAmount);
        }
    }

    /**
     * Get Start Date and End Date of voucher
     * @param $fromDate
     * @param $toDate
     * @return string
     */
    public function getEffectiveOfVoucher($fromDate, $toDate)
    {

        $fromDateFormat = $this->formatDate($fromDate);
        if ($toDate) {
            $toDateFormat = $this->formatDate($toDate);
        } else {
            $toDateFormat = __('Unlimited');
        }

        return $fromDateFormat . ' - ' . $toDateFormat;
    }

    /**
     * @param $date
     * @return false|string
     */
    public function formatDate($date)
    {
        $format = 'd.m.Y';
        return date($format, strtotime($date));
    }

    /**
     * Used for get condition payment method of voucher
     *
     * @param $ruleId
     * @return \Magento\Framework\Phrase|string
     */
    public function getConditionPaymentMethodOfVoucher($ruleId)
    {
        try {
            $rule = $this->getRuleById($ruleId);
            $ruleConditionArray = $rule->getConditions()->asArray();
            $conditionPaymentMethod = $this->searchCondition(['payment_method'], $ruleConditionArray);
            if ($conditionPaymentMethod) {
                if (
                    isset($conditionPaymentMethod['operator']) &&
                    isset($conditionPaymentMethod['value'])
                ) {
                    $operatorText = $this->convertOperatorToText($conditionPaymentMethod['operator']);
                    $paymentTitle = $this->getTitleMethodByCode($conditionPaymentMethod['value']);
                    return __('Payment method') . ' ' . $operatorText . ' ' . $paymentTitle;
                }

            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return __('All payment methods.');
    }

    /**
     * Used for get condition shipping method of voucher
     *
     * @param $ruleId
     * @return \Magento\Framework\Phrase|string
     */
    public function getConditionShippingMethodOfVoucher($ruleId)
    {
        try {
            $rule = $this->getRuleById($ruleId);
            $ruleConditionArray = $rule->getConditions()->asArray();
            $conditionShippingMethod = $this->searchCondition(['shipping_method'], $ruleConditionArray);
            if ($conditionShippingMethod) {
                if (
                    isset($conditionShippingMethod['operator']) &&
                    isset($conditionShippingMethod['value'])
                ) {
                    $operatorText = $this->convertOperatorToText($conditionShippingMethod['operator']);
                    $carrierCode = explode('_', $conditionShippingMethod['value']);
                    $carrierTitle = $this->getTitleMethodByCode($carrierCode[0] ?? '', 'carriers');
                    return __('Shipping method') . ' ' . $operatorText . ' ' . $carrierTitle;
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return __('All shipping methods.');
    }

    /**
     * @param $ruleId
     * @return \Magento\Framework\Phrase|string|null
     */
    public function getConditionLimitUserOfVoucher($ruleId)
    {
        try {
            $rule = $this->getRuleById($ruleId);
            if ($rule->getUsesPerCustomer() || $rule->getUsesPerCoupon()) {
                return __('Limited usage. Hurry up before you miss it!');
            } else {
                return __('Unlimited usage');
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * @param $ruleId
     * @return \Magento\Framework\Phrase|string|null
     */
    public function getConditionProductOfVoucher($ruleId) {
        try {
            $rule = $this->getRuleById($ruleId);
            $actionJson = $this->json->serialize($rule->getActions()->asArray());
            if (preg_match(self::REGEX_CONDITION_PRODUCT, $actionJson)) {
                return __('Applies on app to certain participating products & to certain users.');
            } else {
                return __('Applies for all product');
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * @param $ruleId
     * @return string|null
     */
    public function getConditionOfferOfVoucher($ruleId)
    {
        try {
            $rule = $this->getRuleById($ruleId);
            $ruleConditionArray = $rule->getConditions()->asArray();
            $conditionOffer = $this->searchCondition(['base_subtotal_with_discount', 'base_subtotal', 'base_subtotal_total_incl_tax'], $ruleConditionArray);
            if ($conditionOffer) {
                if (
                    isset($conditionOffer['operator']) &&
                    isset($conditionOffer['value'])
                ) {
                    $operatorText = $this->convertOperatorToText($conditionOffer['operator']);
                    return __('Subtotal') . ' ' . $operatorText . ' ' . $conditionOffer['value'];
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * Function getRuleById
     *
     * Used for save rule in cache and reuse in many function
     *
     * @param $ruleId
     * @return Rule
     */
    public function getRuleById($ruleId)
    {
        if (!$this->ruleCache) {
            $this->ruleCache = $this->ruleFactory->create()->load($ruleId);
        }
        return $this->ruleCache;
    }

    /**
     * Use recursive to search condition in rule condition array
     * @param array $attribute
     * @param array $ruleConditionArray
     * @return array|null
     */
    function searchCondition($attribute, $ruleConditionArray)
    {
        $result = null;
        try {
            if (isset($ruleConditionArray['attribute']) && in_array($ruleConditionArray['attribute'], $attribute)) {
                return [
                    'operator' => $ruleConditionArray['operator'] ?? null,
                    'value' => $ruleConditionArray['value'] ?? null
                ];
            }

            if (isset($ruleConditionArray['conditions']) && is_array($ruleConditionArray['conditions'])) {
                foreach ($ruleConditionArray['conditions'] as $condition) {
                    if (isset($condition['attribute']) && in_array($condition['attribute'], $attribute)) {
                        $result = [
                            'operator' => $condition['operator'] ?? null,
                            'value' => $condition['value'] ?? null
                        ];
                    } else {
                        $this->searchCondition($attribute, $condition);
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        return $result;
    }

    /**
     * @param $operator
     * @return mixed|null
     */
    private function convertOperatorToText($operator)
    {
        $defaultOption = $this->addressCondition->getDefaultOperatorOptions();
        if (isset($defaultOption[$operator])) {
            return $defaultOption[$operator];
        }

        return null;
    }

    /**
     * @param $code
     * @param $type
     * @return mixed
     */
    private function getTitleMethodByCode($code, $type = 'payment')
    {
        return $this->_scopeConfig->getValue(
            "$type/" . $code . '/title'
        );
    }
}
