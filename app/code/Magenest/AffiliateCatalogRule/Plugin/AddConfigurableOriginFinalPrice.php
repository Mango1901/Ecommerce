<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 04/11/2021
 * Time: 15:51
 */

namespace Magenest\AffiliateCatalogRule\Plugin;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\Locale\Format;

class AddConfigurableOriginFinalPrice
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
     * @var Format
     */
    protected $localeFormat;

    /**
     * AddConfigurableOriginFinalPrice constructor.
     * @param \Magento\Framework\Json\DecoderInterface $decoder
     * @param \Magento\Framework\Json\EncoderInterface $encoder
     * @param Format $format
     */
    public function __construct(
        \Magento\Framework\Json\DecoderInterface $decoder,
        \Magento\Framework\Json\EncoderInterface $encoder,
        Format $format
    ) {
        $this->decoder = $decoder;
        $this->encoder = $encoder;
        $this->localeFormat = $format;
    }

    /**
     * @param Configurable $subject
     * @param $result
     * @return string
     */
    public function afterGetJsonConfig(Configurable $subject, $result)
    {
        $resultDecoded = $this->decoder->decode($result);
        if (isset($resultDecoded['optionPrices'])) {
            foreach ($subject->getAllowProducts() as $product) {
                $priceInfo = $product->getPriceInfo();

                $resultDecoded['optionPrices'][$product->getId()]['baseOriginFinalPrice'] = [
                    'amount' => $this->localeFormat->getNumber(
                        $priceInfo->getPrice('origin_rule_price')->getAmount()->getBaseAmount()
                    )
                ];
                $resultDecoded['optionPrices'][$product->getId()]['originFinalPrice'] = [
                    'amount' => $this->localeFormat->getNumber(
                        $priceInfo->getPrice('origin_rule_price')->getAmount()->getValue()
                    )
                ];
            }
            $result = $this->encoder->encode($resultDecoded);
        }
        return $result;
    }
}
