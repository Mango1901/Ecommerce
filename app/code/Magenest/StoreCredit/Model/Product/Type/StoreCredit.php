<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Model\Product\Type;

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Option;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\Virtual;
use Magento\Eav\Model\Config;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magenest\StoreCredit\Helper\Product as HelperData;
use Magenest\StoreCredit\Model\Config\Source\FieldRenderer;
use Psr\Log\LoggerInterface;
use Zend_Serializer_Exception;

/**
 * Class StoreCredit
 * @package Magenest\StoreCredit\Model\Product\Type
 */
class StoreCredit extends Virtual
{
    const TYPE_STORE_CREDIT = 'mpstorecredit';

    /**
     * @type HelperData
     */
    protected $helper;

    /**
     * If product can be configured
     *
     * @var bool
     */
    protected $_canConfigure = true;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * StoreCredit constructor.
     *
     * @param Option $catalogProductOption
     * @param Config $eavConfig
     * @param Type $catalogProductType
     * @param ManagerInterface $eventManager
     * @param Database $fileStorageDb
     * @param Filesystem $filesystem
     * @param Registry $coreRegistry
     * @param LoggerInterface $logger
     * @param ProductRepositoryInterface $productRepository
     * @param HelperData $helper
     */
    public function __construct(
        Option $catalogProductOption,
        Config $eavConfig,
        Type $catalogProductType,
        ManagerInterface $eventManager,
        Database $fileStorageDb,
        Filesystem $filesystem,
        Registry $coreRegistry,
        LoggerInterface $logger,
        ProductRepositoryInterface $productRepository,
        HelperData $helper
    ) {
        $this->helper = $helper;
        $this->logger = $logger;

        parent::__construct(
            $catalogProductOption,
            $eavConfig,
            $catalogProductType,
            $eventManager,
            $fileStorageDb,
            $filesystem,
            $coreRegistry,
            $logger,
            $productRepository
        );
    }

    /**
     * @param Product $product
     *
     * @return $this
     */
    public function beforeSave($product)
    {
        parent::beforeSave($product);

        $product->setTypeHasOptions(true);
        if ($product->getAllowCreditRange()) {
            $product->setTypeHasRequiredOptions(true);
        }

        return $this;
    }

    /**
     * @param DataObject $buyRequest
     * @param Product $product
     * @param string $processMode
     *
     * @return array|Phrase|string
     */
    protected function _prepareProduct(DataObject $buyRequest, $product, $processMode)
    {
        $result = parent::_prepareProduct($buyRequest, $product, $processMode);

        if (is_string($result)) {
            return $result;
        }

        try {
            $result = $this->_prepareStoreCreditData($buyRequest, $product);
        } catch (LocalizedException $e) {
            return $e->getMessage();
        } catch (Exception $e) {
            $this->logger->critical($e);

            return __('Something went wrong.');
        }

        return $result;
    }

    /**
     * @param $buyRequest
     * @param Product $product
     *
     * @return array
     * @throws LocalizedException
     */
    protected function _prepareStoreCreditData($buyRequest, $product)
    {
        $amount = $this->_validateAmount($buyRequest, $product);
        $product->addCustomOption(FieldRenderer::CREDIT_AMOUNT, $amount, $product);
        $product->addCustomOption(FieldRenderer::CREDIT_RATE, $product->getCreditRate(), $product);

        return [$product];
    }

    /**
     * @param $buyRequest
     * @param Product $product
     *
     * @return mixed
     * @throws LocalizedException
     */
    protected function _validateAmount($buyRequest, $product)
    {
        $amount = $product->getAllowCreditRange() ? $product->getMinCredit() : $product->getCreditAmount();
        $amount = $buyRequest->getCreditAmount() ?: $amount;

        if (!!$buyRequest->getCreditRange()) {
            if ($product->getAllowCreditRange()) {
                $minAmount = $product->getMinCredit();
                $maxAmount = $product->getMaxCredit();
                if ($amount <= 0 || ($minAmount && ($amount < $minAmount)) || ($maxAmount && ($amount > $maxAmount))) {
                    throw new LocalizedException(__('Range amount is incorrect, please choose your range amount again'));
                }
            } else {
                throw new LocalizedException(__('Range amount is not allowed'));
            }
        }

        return $amount;
    }

    /**
     * Check if product can be bought
     *
     * @param Product $product
     *
     * @return $this
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function checkProductBuyState($product)
    {
        parent::checkProductBuyState($product);
        $option = $product->getCustomOption('info_buyRequest');
        if ($option instanceof \Magento\Quote\Model\Quote\Item\Option) {
            $buyRequest = new DataObject($this->helper->unserialize($option->getValue()));
            $this->_prepareStoreCreditData($buyRequest, $product);
        }

        return $this;
    }

    /**
     * @param Product $product
     * @param DataObject $buyRequest
     *
     * @return array
     */
    public function processBuyRequest($product, $buyRequest)
    {
        $options = [
            FieldRenderer::CREDIT_AMOUNT => $buyRequest->getCreditAmount(),
            FieldRenderer::CREDIT_RANGE => !!$product->getCreditRange(),
            FieldRenderer::CREDIT_RATE => $product->getCreditRate()
        ];

        return $options;
    }
}
