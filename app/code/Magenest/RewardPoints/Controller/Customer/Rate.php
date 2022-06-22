<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 * @category Magenest
 * @package  Magenest_RewardPoints
 */
namespace Magenest\RewardPoints\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Catalog\Model\ProductFactory;
use Magenest\RewardPoints\Helper\Data;

/**
 * Get exchange rate for multi-currency stores
 * Class Rate
 * @package Magenest\RewardPoints\Controller\Customer
 */
class Rate extends Action
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var JsonFactory
     */
    protected $jsonHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var
     */
    protected $productFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Rate constructor.
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param JsonFactory $jsonHelper
     * @param ProductFactory $productFactory
     * @param Data $helper
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        JsonFactory $jsonHelper,
        ProductFactory $productFactory,
        Data $helper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->productFactory = $productFactory;
        $this->helper = $helper;
        $this->productRepository = $productRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $result = [];
        $result['rate'] = $this->getRate();

        $data = $this->getRequest()->getParams();
        $product = $this->productRepository->getById($data['id']);
        $childId = $this->getProductId($product, $data);
        $result['child_id'] = $childId;
        if ($childId > 0) {
            $rule = $this->helper->getProductRule($childId);
            $result['rule'] = $rule;
        }

        $resultJson = $this->jsonHelper->create();
        return $resultJson->setData($result);
    }

    /**
     * Get product id from selected option
     *
     * @param $product
     * @param $data
     * @return int
     */
    public function getProductId($product, $data) {
        $childrenProducts = $product->getTypeInstance()->getUsedProducts($product);
        if (empty($data['data_post']) || empty($childrenProducts)) return 0;
        $attributes = explode('|', $data['data_post']);
        if (empty($attributes)) return 0;
        foreach ($attributes as $key => $attribute) {
            $attribute = explode(',', $attribute);
            if (count($attribute) !== 2) return 0;
            for ($i = 0; $i < 2; $i++) {
                if (empty($attribute[$i])) return 0;
                $attributePairs[$key][] = $attribute[$i];
            }
        }
        if (empty($attributePairs)) return 0;

        $numberOfAttributes = count($attributes);
        foreach ($childrenProducts as $child) {
            for ($i = 0; $i < $numberOfAttributes; $i++) {
                // get value according to code
                $attributeValue = $child->getData($attributePairs[$i][0]);

                if ($i === $numberOfAttributes - 1) {
                    // compare
                    if ($attributeValue === $attributePairs[$i][1])
                        return $child->getEntityId();
                } else {
                    // compare
                    if (!($attributeValue === $attributePairs[$i][1])) continue;
                }
            }
        }

        return 0;

    }

    /**
     * Get list of product matching attribute
     *
     * @param $childrenProducts
     * @param $code
     * @param $value
     * @return array
     */
    public function getChildren($childrenProducts, $code, $value) {
        $collection = [];
        foreach ($childrenProducts as $child) {
            $attributeValue = $child->getData($code);
            if ($attributeValue == $value) {
                $collection[] = $child;
            }
        }

        return $collection;
    }

    /**
     * Get rate
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRate() {
        $currency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();

        $rate = $this->storeManager->getStore()->getBaseCurrency()->getRate($currency);
        if (!isset($rate)) {
            $rate = 1;
        }

        return $rate;
    }

}