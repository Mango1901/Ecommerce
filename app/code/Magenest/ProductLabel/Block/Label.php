<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Block;

use Magento\Catalog\Model\Product;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;
use Magenest\ProductLabel\Api\Data\ConstantInterface;

class Label extends \Magento\Framework\View\Element\Template implements IdentityInterface
{
    const PERCENT = 'percent';

    protected $_template = 'Magenest_ProductLabel::label.phtml';

    /**
     * @var \Magenest\ProductLabel\Api\LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    private $currency;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_productImageHelper;

    /**
     * @var \Magenest\ProductLabel\Model\Label
     */
    protected $label;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * Label constructor.
     * @param Template\Context $context
     * @param \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Magento\Catalog\Helper\Image $productImageHelper
     * @param \Magenest\ProductLabel\Model\Label $label
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Catalog\Helper\Image $productImageHelper,
        \Magenest\ProductLabel\Model\Label $label,
        \Magento\Catalog\Model\Session $catalogSession,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->labelRepository = $labelRepository;
        $this->currency = $currency;
        $this->_productImageHelper= $productImageHelper;
        $this->label = $label;
        $this->catalogSession = $catalogSession;
    }

    /**
     * @return array|\Magenest\ProductLabel\Api\Data\LabelInterface|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLabel()
    {
        $labelObject = $this->getData('label_object');
        if ($this->getData('label_object')) {
            $this->catalogSession->setLabelId($labelObject->getData('label_id'));
            return $this->getData('label_object');
        }
        return $this->labelRepository->get($this->getLabelId());
    }

    /**
     * @param $label
     * @param $page
     * @return DataObject
     */
    public function getDataLabel($label, $page)
    {
        $data = $label->getData($page);
        $data['label_type'] = $label->getData('label_type');
        $object = new DataObject($data);

        return $object;
    }

    /**
     * @param $name
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSrcImg($name)
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $baseUrl . 'label/tmp/image/' . $name;
    }

    /**
     * @param $name
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSrcShape($name)
    {
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

        return $baseUrl . 'label/tmp/shape/' . $name;
    }

    /**
     * @param $data
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShapeHtml($data)
    {
        $shapeType      = $data->getShapeType();
        $ShapeID        = '#' . $shapeType;
        $customCss      = $data->getData('custom_css');
        $textClass      = "";
        $html           = '';
        $viewBox        = '0 0 180 60';
        switch ($shapeType) {
            case ConstantInterface::SHAPE_NEW_7:
                $viewBox        = '0 0 60 60';
                break;
            case ConstantInterface::SHAPE_NEW_8:
                $viewBox        = '0 0 60 60';
                break;
            case ConstantInterface::SHAPE_NEW_12:
                $viewBox        = '0 0 45 60';
                break;
            case ConstantInterface::SHAPE_NEW_13:
                $viewBox        = '0 0 45 60';
                break;
            case ConstantInterface::SHAPE_NEW_14:
                $viewBox        = '0 0 45 60';
                break;
            case ConstantInterface::SHAPE_NEW_16:
                $viewBox        = '0 0 120 60';
                break;
            case ConstantInterface::SHAPE_NEW_17:
                $viewBox        = '0 0 60 60';
                break;
            case ConstantInterface::SHAPE_NEW_18:
                $viewBox        = '0 0 41.398 60';
                break;
            case ConstantInterface::SHAPE_NEW_19:
                $viewBox        = '0 0 38.421 60';
                break;
            case ConstantInterface::SHAPE_NEW_20:
                $viewBox        = '0 0 60 60';
                break;
        }
        $txt_style = [
            $data->getTextFont() ? 'font-family:' . $data->getTextFont() . ';' : '',
            $data->getTextSize() ? 'font-size:' . $data->getTextSize() . 'px;' : '',
            $data->getTextColor() ? 'color:' . $data->getTextColor() . ';' : '',
        ];

        $textValue = $this->getTextValue($data);
        if ($customCss != '') {
            $html = '<div class="shape-wrapper ' . $shapeType . '" style="' . $customCss . '">';
        } else {
            $html = '<div class="shape-wrapper ' . $shapeType . '">';
        }
        $html .= '<svg fill="' . $data->getShapeColor() . '" class="svg-icon" viewBox="' . $viewBox . '" xml:space="preserve"><use x="0" y="0" xlink:href="' . $ShapeID . '"></use></svg>';
        $html .= '<div class="label-text ' . $textClass . '" style="' . implode('', array_filter($txt_style)) . '"><span>' . $textValue . '</span></div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @param $data
     * @return string|string[]
     */
    public function getTextValue($data)
    {
        $product = $this->getProduct();
        $text = $data->getText();
        $pattern = '/{{([a-zA-Z:\s]+)}}/';
        preg_match_all($pattern, $text, $variable);
        if (!$variable[1]) {
            return $text;
        }
        $variables = $variable[1];
        foreach ($variables as $variable) {
            $labelType = $data->getData('label_type');
            $value = $this->getVariableTextValue($variable, $product, $labelType);
            $text = str_replace('{{' . $variable . '}}', $value, $text);
        }

        return $text;
    }

    /**
     * @param $var
     * @param $product
     * @return mixed
     */
    public function getVariableTextValue($var, $product, $labelType)
    {
        $var = trim($var);
        $var = strtolower($var);
        if ($labelType == ConstantInterface::PRODUCT_LABEL_SALE_TYPE) {
            if ($var == self::PERCENT) {
                return $this->getSalesPercent($var, $product);
            } else {
                return $this->getSalesAmount($var, $product);
            }
        } else {
            return $var;
        }
    }

    /**
     * @param $var
     * @param $product
     * @return string
     */
    public function getSalesPercent($var, $product)
    {
        if (($product instanceof Product)) {
            $productType = $product->getTypeId();
            $regularPrice = $product->getPriceInfo()->getPrice('regular_price');
            $finalPrice = $product->getPriceInfo()->getPrice('final_price');

            switch ($productType) {
                case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                    $minimalRegularPrice = $regularPrice->getMinimalPrice()->getValue();
                    $minimalFinalPrice = $finalPrice->getMinimalPrice()->getValue();
                    $maximalRegularPrice = $regularPrice->getMaximalPrice()->getValue();
                    $maximalFinalPrice = $finalPrice->getMaximalPrice()->getValue();
                    if ($minimalRegularPrice && $minimalFinalPrice && (float)$minimalFinalPrice <= (float)$minimalRegularPrice) {
                        $fixedMinimalPrice = floor((($minimalRegularPrice - $minimalFinalPrice) / $minimalRegularPrice) * 100);
                    }
                    if ($maximalRegularPrice && $maximalFinalPrice && (float)$maximalFinalPrice <= (float)$maximalRegularPrice) {
                        $fixedMaximalPrice = floor((($maximalRegularPrice - $maximalFinalPrice) / $maximalRegularPrice) * 100);
                    }
                    if ($fixedMinimalPrice && $fixedMaximalPrice) {
                        if ($fixedMaximalPrice == $fixedMinimalPrice) {
                            return $fixedMinimalPrice . '%';
                        }
                        return __('from ') . min($fixedMinimalPrice, $fixedMaximalPrice) . '%' . __(' to ') . max($fixedMinimalPrice, $fixedMaximalPrice) . '%';
                    }

                case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                    $subProducts = $product->getTypeInstance()->getAssociatedProducts($product);
                    foreach ($subProducts as $subProduct) {
                        $fixedPercent[] = $this->getFixedPrice($var, $subProduct);
                    }
                    if ($fixedPercent && min($fixedPercent) == max($fixedPercent)) {
                        return min($fixedPercent) . '%';
                    } elseif ($fixedPercent) {
                        return __('from ') . min($fixedPercent) . '%' . __(' to ') . max($fixedPercent) . '%';
                    }

                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                    $subProducts = $product->getTypeInstance()->getUsedProducts($product);
                    foreach ($subProducts as $subProduct) {
                        $fixedPercent[] = $this->getFixedPrice($var, $subProduct);
                    }
                    if ($fixedPercent && min($fixedPercent) == max($fixedPercent)) {
                        return min($fixedPercent) . '%';
                    } elseif ($fixedPercent) {
                        return __('from ') . min($fixedPercent) . '%' . __(' to ') . max($fixedPercent) . '%';
                    }

                default:
                    $fixedPercent = $this->getFixedPrice($var, $product);
                    if (is_numeric($fixedPercent)) {
                        return $fixedPercent . '%';
                    }
            }
        }

        return $var;
    }

    /**
     * @param $var
     * @param $product
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getSalesAmount($var, $product)
    {
        if ($product instanceof Product) {
            $productType = $product->getTypeId();
            $currencyCode = $this->_storeManager->getStore()->getCurrentCurrencyCode();
            $currency = $this->currency->load($currencyCode);
            $regularPrice = $product->getPriceInfo()->getPrice('regular_price');
            $finalPrice = $product->getPriceInfo()->getPrice('final_price');

            switch ($productType) {
                case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                    $minimalRegularPrice = $regularPrice->getMinimalPrice()->getValue();
                    $minimalFinalPrice = $finalPrice->getMinimalPrice()->getValue();
                    $maximalRegularPrice = $regularPrice->getMaximalPrice()->getValue();
                    $maximalFinalPrice = $finalPrice->getMaximalPrice()->getValue();
                    if ($minimalRegularPrice && $minimalFinalPrice && (float)$minimalFinalPrice <= (float)$minimalRegularPrice) {
                        $fixedMinimalPrice =  $minimalRegularPrice - $minimalFinalPrice;
                    }
                    if ($maximalRegularPrice && $maximalFinalPrice && (float)$maximalFinalPrice <= (float)$maximalRegularPrice) {
                        $fixedMaximalPrice =  $maximalRegularPrice - $maximalFinalPrice;
                    }
                    if ($fixedMinimalPrice && $fixedMaximalPrice) {
                        if ($fixedMaximalPrice == $fixedMinimalPrice) {
                            return $currency->format($fixedMinimalPrice, [], false);
                        }
                        return __('from ') . $currency->format(min($fixedMinimalPrice, $fixedMaximalPrice), [], false) . __(' to ') . $currency->format(max($fixedMinimalPrice, $fixedMaximalPrice), [], false);
                    }

                case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                    $subProducts = $product->getTypeInstance()->getAssociatedProducts($product);
                    foreach ($subProducts as $subProduct) {
                        $fixedAmount[] = $this->getFixedPrice($var, $subProduct);
                    }
                    if ($fixedAmount && min($fixedAmount) == max($fixedAmount)) {
                        return $currency->format(min($fixedAmount), [], false);
                    } elseif ($fixedAmount) {
                        return __('from ') . $currency->format(min($fixedAmount), [], false) . __(' to ') . $currency->format(max($fixedAmount), [], false);
                    }

                case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                    $subProducts = $product->getTypeInstance()->getUsedProducts($product);
                    foreach ($subProducts as $subProduct) {
                        $fixedPercent[] = $this->getFixedPrice($var, $subProduct);
                    }
                    if ($fixedPercent && min($fixedPercent) == max($fixedPercent)) {
                        return min($fixedPercent) . '%';
                    } elseif ($fixedPercent) {
                        return __('from ') . min($fixedPercent) . '%' . __(' to ') . max($fixedPercent) . '%';
                    }

                default:
                    $fixedAmount = $this->getFixedPrice($var, $product);
                    if (is_numeric($fixedAmount)) {
                        return $currency->format($fixedAmount, [], false);
                    }
            }
        }

        return $var;
    }

    /**
     * @param $var
     * @param $product
     * @return false|float
     */
    private function getFixedPrice($var, $product)
    {
        $regularPrice = $product->getPriceInfo()->getPrice('regular_price')->getValue();
        $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getValue();
        if ($finalPrice && $regularPrice && (float)$finalPrice <= (float)$regularPrice) {
            if ($var == 'percent') {
                return floor((($regularPrice - $finalPrice) / $regularPrice) * 100);
            } elseif ($var == 'amount') {
                return $regularPrice - $finalPrice;
            }
        }
    }

    /**
     * @param $labelType
     * @param $data
     * @return string
     */
    public function getLabelLayout($labelType, $data) {
        $labelLayout   = 'text-only';
        if ($labelType == 2) {
            $labelLayout = $data->getShapeType();
        } elseif ($labelType == 3) {
            $labelLayout = 'image-layout';
        }
        return $labelLayout;
    }

    /**
     * @param $data
     * @param $labelLayout
     * @return array
     */
    public function getLabelClass($data, $labelLayout) {
        $position = $data->getData('position');
        return [
            'mgn-product-label',
            'product-label-container',
            $position,
            $labelLayout
        ];
    }

    /**
     * @param $data
     * @param $labelLayout
     * @return string[]
     */
    public function getLabelStyle($data, $labelLayout) {
        $labelSize  = $data->getLabelSize();
        $height     = ($labelLayout != 'image-layout' && $labelLayout != 'text-only') ? 'height:0;' : '';
        $pd_bottom  = ($labelLayout == 'shape-rectangle') ? 'padding-bottom:' . ($labelSize / 2) . 'px;' : 'padding-bottom:' . $labelSize . 'px;';
        $pd_bottom  = ($labelLayout != 'image-layout' && $labelLayout != 'text-only') ? $pd_bottom : '';

        return [
            'width:' . $labelSize . 'px;',
            $height,
            $pd_bottom,
        ];
    }

    /**
     * @param $data
     * @return string[]
     */
    public function getTxtStyle($data) {
        $textFont = $data->getTextFont();
        $textSize = $data->getTextSize();
        $textColor = $data->getTextColor();
        return [
            $textFont ? 'font-family:' . $textFont . ';' : '',
            'font-size:' . $textSize. 'px;',
            'color:' . $textColor . ';',
        ];
    }

    /**
     * @param $product
     * @param $imageId
     * @param array $attributes
     * @return array|null
     */
    public function getImageSize($product, $imageId, $attributes = [])
    {
        if ($product->getThumbnail()) {
            return [
                'width' => $this->_productImageHelper->init($product, $imageId, $attributes)->getWidth(),
                'height' => $this->_productImageHelper->init($product, $imageId, $attributes)->getHeight()
            ];
        }
        return null;
    }

    /**
     * @return array|string[]
     */
    public function getIdentities()
    {
        return [
            $this->label::CACHE_TAG,
            $this->label::CACHE_TAG . '_' . $this->catalogSession->getData('label_id')
        ];
    }

    /**
     * @param $product
     * @return bool
     */
    public function checkImageThumb($product) {
        return $product->getMediaGallery('images');
    }
}
