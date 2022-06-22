<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 * Created by PhpStorm.
 * User: crist
 * Date: 28/10/2021
 * Time: 15:56
 */

namespace Magenest\AffiliateCatalogRule\Model\Index;

use Magenest\Affiliate\Api\ConfigRepositoryInterface;
use Magento\CatalogRule\Model\Indexer\ProductPriceCalculator;
use Magento\CatalogRule\Model\Indexer\ReindexRuleProductPrice;
use Magento\CatalogRule\Model\Indexer\RuleProductPricesPersistor;
use Magento\CatalogRule\Model\Indexer\RuleProductsSelectBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class ReindexRuleProductPriceWithAffiliate extends ReindexRuleProductPrice
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RuleProductsSelectBuilder
     */
    private $ruleProductsSelectBuilder;

    /**
     * @var ProductPriceCalculator
     */
    private $productPriceCalculator;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var RuleProductPricesPersistor
     */
    private $pricesPersistor;

    /**
     * @var bool
     */
    private $useWebsiteTimezone;

    /** @var ConfigRepositoryInterface */
    private $configRepository;

    /**
     * @param StoreManagerInterface $storeManager
     * @param RuleProductsSelectBuilder $ruleProductsSelectBuilder
     * @param ProductPriceCalculator $productPriceCalculator
     * @param TimezoneInterface $localeDate
     * @param RuleProductPricesPersistor $pricesPersistor
     * @param ConfigRepositoryInterface $configRepository
     * @param bool $useWebsiteTimezone
     */
    public function __construct(
        StoreManagerInterface      $storeManager,
        RuleProductsSelectBuilder  $ruleProductsSelectBuilder,
        ProductPriceCalculator     $productPriceCalculator,
        TimezoneInterface          $localeDate,
        RuleProductPricesPersistor $pricesPersistor,
        ConfigRepositoryInterface  $configRepository,
        bool                       $useWebsiteTimezone = true
    ) {
        $this->storeManager              = $storeManager;
        $this->ruleProductsSelectBuilder = $ruleProductsSelectBuilder;
        $this->productPriceCalculator    = $productPriceCalculator;
        $this->localeDate                = $localeDate;
        $this->pricesPersistor           = $pricesPersistor;
        $this->useWebsiteTimezone        = $useWebsiteTimezone;
        $this->configRepository          = $configRepository;
    }

    /**
     * Reindex product prices.
     *
     * @param int $batchCount
     * @param int|null $productId
     * @param bool $useAdditionalTable
     *
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @throws \Zend_Db_Statement_Exception
     * @throws \Exception
     */
    public function execute(int $batchCount, ?int $productId = null, bool $useAdditionalTable = false)
    {
        /**
         * Update products rules prices per each website separately
         * because for each website date in website's timezone should be used
         */
        $affiliateGroup = explode(",", $this->configRepository->get()['general']['affiliate_group']);
        foreach ($this->storeManager->getWebsites() as $website) {
            $productsStmt     = $this->ruleProductsSelectBuilder->build(
                $website->getId(),
                $productId,
                $useAdditionalTable
            );
            $dayPrices        = [];
            $originPrices     = [];
            $originPricesKey  = [];
            $minimumByProduct = [];
            $keyByProduct     = [];
            $stopFlags        = [];

            $storeGroup   = $this->storeManager->getGroup($website->getDefaultGroupId());
            $dateInterval = $this->useWebsiteTimezone
                ? $this->getDateInterval((int)$storeGroup->getDefaultStoreId())
                : $this->getDateInterval(Store::DEFAULT_STORE_ID);

            while ($ruleData = $productsStmt->fetch()) {
                $ruleProductId = $ruleData['product_id'];
                $productKey    = $ruleProductId .
                    '_' .
                    $ruleData['website_id'] .
                    '_' .
                    $ruleData['customer_group_id'];

                /**
                 * Build prices for each day
                 */
                foreach ($dateInterval as $date) {
                    $time = $date->getTimestamp();
                    if (($ruleData['from_time'] == 0 || $time >= $ruleData['from_time'])
                        && ($ruleData['to_time'] == 0 || $time <= $ruleData['to_time'])
                    ) {
                        $priceKey = $time . '_' . $productKey;

                        if (isset($stopFlags[$priceKey])) {
                            continue;
                        }

                        if (!isset($dayPrices[$priceKey])) {
                            $rulePrice = $originRulePrice = $this->productPriceCalculator->calculate($ruleData);
                            if ($ruleData['is_affiliate']) {
                                $originRulePrice                 = $ruleData['default_price'];
                                $originPricesKey[$ruleProductId] = $priceKey;
                                if (isset($originPrices[$ruleProductId])) {
                                    $originRulePrice = $originPrices[$ruleProductId];
                                }
                            }
                            if (!in_array($ruleData['customer_group_id'], $affiliateGroup)) {
                                $originPrices[$ruleProductId] = min(
                                    $originPrices[$ruleProductId] ?? $rulePrice,
                                    $rulePrice
                                );
                            }

                            $dayPrices[$priceKey] = [
                                'rule_date'         => $date,
                                'website_id'        => $ruleData['website_id'],
                                'customer_group_id' => $ruleData['customer_group_id'],
                                'product_id'        => $ruleProductId,
                                'rule_price'        => $rulePrice,
                                'origin_rule_price' => $originRulePrice,
                                'latest_start_date' => $ruleData['from_time'],
                                'earliest_end_date' => $ruleData['to_time'],
                            ];
                            if (!in_array($ruleData['customer_group_id'], $affiliateGroup)) {
                                $minimumByProduct[$ruleProductId] = min(
                                    $originPrices[$ruleProductId] ?? $rulePrice,
                                    $rulePrice
                                );
                            }
                        } else {
                            $dayPrices[$priceKey]['rule_price']        = $this->productPriceCalculator->calculate(
                                $ruleData,
                                $dayPrices[$priceKey]
                            );
                            $dayPrices[$priceKey]['latest_start_date'] = max(
                                $dayPrices[$priceKey]['latest_start_date'],
                                $ruleData['from_time']
                            );
                            $dayPrices[$priceKey]['earliest_end_date'] = min(
                                $dayPrices[$priceKey]['earliest_end_date'],
                                $ruleData['to_time']
                            );
                            if (!in_array($ruleData['customer_group_id'], $affiliateGroup)) {
                                $originPrices[$ruleProductId] = min(
                                    $originPrices[$ruleProductId],
                                    $dayPrices[$priceKey]['rule_price']
                                );
                                if (isset($originPricesKey[$ruleProductId])) {
                                    $dayPrices
                                    [$originPricesKey[$productId] ?? $originPricesKey[$ruleProductId]]
                                    ['origin_rule_price'] = $originPrices[$ruleProductId];
                                }
                            }
                            if ($ruleData['is_affiliate']) {
                                $dayPrices[$priceKey]['origin_rule_price'] =
                                    $originPrices[$ruleProductId] ?? $dayPrices[$priceKey]['rule_price'];
                                $originPricesKey[$ruleProductId]           = $priceKey;
                            }
                            if (!in_array($ruleData['customer_group_id'], $affiliateGroup)) {
                                $minimumByProduct[$ruleProductId] = min(
                                    $originPrices[$ruleProductId] ?? $dayPrices[$priceKey]['rule_price'],
                                    $dayPrices[$priceKey]['rule_price']
                                );
                            }
                        }
                        if ($ruleData['is_affiliate']) {
                            $keyByProduct[$ruleProductId][$priceKey] = $priceKey;
                        }
                        if ($ruleData['action_stop']) {
                            $stopFlags[$priceKey] = true;
                        }
                    }
                }
            }

            foreach ($minimumByProduct as $productId2 => $minPrice) {
                if (isset($keyByProduct[$productId2])) {
                    foreach ($keyByProduct[$productId2] as $key) {
                        if (isset($dayPrices[$key])) {
                            $dayPrices[$key]['origin_rule_price'] = $minPrice;
                        }
                    }
                }
            }

            $this->pricesPersistor->execute($dayPrices, $useAdditionalTable);
        }

        return true;
    }

    /**
     * Retrieve date sequence in store time zone
     *
     * @param int $storeId
     *
     * @return \DateTime[]
     */
    private function getDateInterval(int $storeId): array
    {
        $currentDate  = $this->localeDate->scopeDate($storeId, null, true);
        $previousDate = (clone $currentDate)->modify('-1 day');
        $previousDate->setTime(23, 59, 59);
        $nextDate = (clone $currentDate)->modify('+1 day');
        $nextDate->setTime(0, 0, 0);

        return [$previousDate, $currentDate, $nextDate];
    }
}
