<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 04/11/2021
 * Time: 15:30
 */

namespace Magenest\AffiliateCatalogRule\Plugin;


use Magento\Catalog\Block\Product\View;
use Magento\GroupedProduct\Model\Product\Type\Grouped;

class AddOriginalFinalPrice
{
    /**
     * @var \Magento\Framework\Json\DecoderInterface
     */
    protected $decoder;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $encoder;

    /**
     * AddOriginalFinalPrice constructor.
     * @param \Magento\Framework\Json\DecoderInterface $decoder
     * @param \Magento\Framework\Json\EncoderInterface $encoder
     */
    public function __construct(
        \Magento\Framework\Json\DecoderInterface $decoder,
        \Magento\Framework\Json\EncoderInterface $encoder
    ) {
        $this->decoder = $decoder;
        $this->encoder = $encoder;
    }

    /**
     * @param \Magento\Catalog\Block\Product\View $subject
     * @param $result
     */
    public function afterGetJsonConfig(\Magento\Catalog\Block\Product\View $subject, $result)
    {
        $resultDecoded = $this->decoder->decode($result);
        if (isset($resultDecoded['prices'])) {
            $product = $subject->getProduct();
            $priceInfo = $product->getPriceInfo();
            $originRulePrice = false;
            if ($product->getTypeId() != Grouped::TYPE_CODE) {
                $originRulePrice = $priceInfo->getPrice('origin_rule_price')->getAmount();
            }

            $resultDecoded['prices'] = array_merge_recursive($resultDecoded['prices'], [
                'baseOriginFinalPrice' => [
                    'amount'      => $originRulePrice ? $originRulePrice->getBaseAmount() * 1 : 0,
                    'adjustments' => []
                ],
                'originFinalPrice' => [
                    'amount'      => $originRulePrice ? $originRulePrice->getValue() * 1 : 0,
                    'adjustments' => []
                ]
            ]);
            $result = $this->encoder->encode($resultDecoded);
        }

        return $result;
    }
}
