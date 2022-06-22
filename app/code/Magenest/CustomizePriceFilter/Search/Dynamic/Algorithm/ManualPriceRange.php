<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 22/11/2021
 * Time: 11:47
 */

namespace Magenest\CustomizePriceFilter\Search\Dynamic\Algorithm;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Search\Adapter\OptionsInterface;
use Magento\Framework\Search\Dynamic\Algorithm\AlgorithmInterface;
use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Dynamic\EntityStorage;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Serialize\SerializerInterface;

class ManualPriceRange implements AlgorithmInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var OptionsInterface
     */
    private $options;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * ManualPriceRange constructor.
     * @param DataProviderInterface $dataProvider
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer
    ) {
        $this->dataProvider = $dataProvider;
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(
        BucketInterface $bucket,
        array $dimensions,
        EntityStorage $entityStorage
    ) {
        $dbRanges = $this->dataProvider->getAggregation($bucket, $dimensions, 100, $entityStorage);
        $dbRanges = $this->processRange($dbRanges);
        $data = $this->prepareData($dbRanges);

        return $data;
    }

    /**
     * @param array $items
     * @return array
     */
    private function processRange($items)
    {
        $options = $this->scopeConfig->getValue('catalog/layered_navigation/price_options');
        $options = $this->serializer->unserialize($options);
        $result = [];
        ksort($items);
        foreach ($options as $option) {
            if (!isset($result[$option['price_option']])) {
                $result[$option['price_option']] = 0;
            }
            foreach ($items as $key => $count) {
                if ($option['price_option']/100 >= $key) {
                    $result[$option['price_option']] += $count;
                    unset($items[$key]);
                } else {
                    break;
                }
            }
        }
        return $result;
    }

    private function prepareData($dbRanges)
    {
        $data = [];
        if (!empty($dbRanges)) {
            $i = 0;
            $toPrice = 0;
            foreach ($dbRanges as $index => $count) {
                $fromPrice = $i ? $toPrice : 0;
                $toPrice = $index;
                $data[] = [
                    'from' => $fromPrice,
                    'to' => $toPrice,
                    'count' => $count,
                ];
                $i++;
            }
        }

        return $data;
    }
}
