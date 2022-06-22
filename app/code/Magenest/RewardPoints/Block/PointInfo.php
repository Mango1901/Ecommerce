<?php

namespace Magenest\RewardPoints\Block;

use Magento\Customer\Helper\Session\CurrentCustomer;

/**
 * Class PointInfo
 * @package Magenest\RewardPoints\Block
 */
class PointInfo extends \Magento\Catalog\Block\Product\AbstractProduct implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var string
     */
    protected $_template = "";

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_helper;

    /**
     * @var CurrentCustomer
     */
    protected $_currentCustomer;

    /**
     * @var \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $listProduct;

    protected $validProductRules;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serialize;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * @var \Magenest\RewardPoints\Model\AccountFactory
     */
    protected $accountFactory;

    /**
     * PointInfo constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param CurrentCustomer $currentCustomer
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Catalog\Block\Product\ListProduct $listProduct
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Customer\Model\Session $session
     * @param \Magenest\RewardPoints\Model\AccountFactory $accountFactory
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        CurrentCustomer $currentCustomer,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magenest\RewardPoints\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Customer\Model\Session $session,
        \Magenest\RewardPoints\Model\AccountFactory $accountFactory
    ) {
        $this->_helper          = $helper;
        $this->_currentCustomer = $currentCustomer;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->listProduct = $listProduct;
        $this->serialize = $serialize;
        $this->productFactory = $productFactory;
        $this->session = $session;
        $this->accountFactory = $accountFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function getCurrentProductId()
    {
        $id = $this->_coreRegistry->registry('current_product')->getId();

        return $id;
    }

    /**
     * @return mixed
     */
    public function getCurrentProductType()
    {
        $typeId = $this->_coreRegistry->registry('current_product')->getTypeId();

        return $typeId;
    }

    /**
     * @return bool
     */
    public function getIsShowProductDetailEnabled()
    {
        return $this->_helper->isShowProductDetailEnabled();
    }

    /**
     * @return bool
     */
    public function isShowProductListEnabled()
    {
        return $this->_helper->isShowProductListEnabled();
    }

    /**
     * @return bool
     */
    public function isShowPointForGuest()
    {
        if (empty($this->session->getCustomerId())) {
            return  $this->_helper->isShowPointForGuest();
        }

        return true; // always display for customer
    }

    public function isShowPointInProductList()
    {
        $isShowPoint = $this->isShowProductListEnabled() && $this->getEnableModule();

        return $isShowPoint && $this->isShowPointForGuest();
    }

    /**
     * @param $productId
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductPoint($productId)
    {
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $this->productFactory->create()->load($productId);
        $productPoint = $this->_helper->getProductPoints($productId);
        if ($product->getTypeId() == 'bundle') {
            $arrayPoint = ['min' => 0, 'max' => 0];
            $upOrDown   = $this->_helper->getUpOrDown();
            if (is_array($productPoint) AND count($productPoint) > 0) {
                foreach ($productPoint as $points) {
                    if ($upOrDown == 'up')
                        $arrayPoint = ['min' => (ceil(min($points)) + $arrayPoint['min']), 'max' => (ceil(max($points)) + $arrayPoint['max'])];
                    else
                        $arrayPoint = ['min' => (floor(min($points)) + $arrayPoint['min']), 'max' => (floor(max($points)) + $arrayPoint['max'])];

                }
            }


            return ['min' => $arrayPoint['min'], 'max' => $arrayPoint['max']];
        } else {
            $upOrDown = $this->_helper->getUpOrDown();
            if ($upOrDown == 'up') {
                return ['min' => ceil($productPoint), 'max' => ceil($productPoint)];
            }

            return ['min' => floor($productPoint), 'max' => floor($productPoint)];
        }
    }

    /**
     * @param $productId
     *
     * @return array|float|int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPointBundleProduct($productId)
    {
        /**
         * @var \Magento\Catalog\Model\Product $product
         */
        $product = $this->productFactory->create()->load($productId);
        $productPoint = [];
        if ($product->getTypeId() == 'bundle') {
            $productPoint = $this->_helper->getProductPoints($productId);
        }

        return $productPoint;
    }


    public function getDefaultBundlePoint($productId)
    {
        $productPoints = $this->getPointBundleProduct($productId);
        $point         = 0;
        $upOrDown      = $this->_helper->getUpOrDown();
        foreach ($productPoints as $productPoint) {
            reset($productPoints);
            if ($upOrDown == 'up') {
                $point += ceil(current($productPoint));
            } else {
                $point += floor(current($productPoint));
            }
        }

        return $point;
    }

    /**
     * @return int
     */
    public function getDiscountPercent()
    {
        $customerSession = $this->session;
        $customerPoints  = $this->getCustomerPoints($customerSession->getId());

        return $this->_helper->getDiscountPercent($customerPoints);
    }

    /**
     * @param $customerId
     *
     * @return mixed
     */
    public function getCustomerPoints($customerId)
    {
        /**
         * @var \Magenest\RewardPoints\Model\Account $accountModel
         */
        $accountModel = $this->accountFactory->create()->load($customerId, 'customer_id');

        return $accountModel->getData('points_current');
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return $this->getUrl('rewardpoints/customer/rate');
    }

    /**
     * @return array|bool|float|int|mixed|string|null
     */
    public function getDiscountInfo()
    {
        $checkData = $this->_helper->getDiscountInfo();
        if (isset($checkData)) {
            return $this->serialize->unserialize($checkData);
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getRewardTiersEnable()
    {
        return $this->_helper->getRewardTiersEnable();
    }

    /**
     * @return mixed
     */
    public function getUpOrDown()
    {
        return $this->_helper->getUpOrDown();

    }

    public function getPointColor()
    {
        return $this->_helper->getPointColor();
    }

    public function getPointUnit()
    {
        return $this->_helper->getPointUnit();
    }

    public function getPointSize()
    {
        return $this->_helper->getPointSize();
    }

    /**
     * @return bool|mixed
     */
    public function getEnableModule()
    {
        return $this->_helper->getEnableModule();
    }

    /**
     * Get currency rate
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencyRate() {
        $currency = $this->_storeManager->getStore()->getCurrentCurrency()->getCode();

        $rate = $this->_storeManager->getStore()->getBaseCurrency()->getRate($currency);
        if (!isset($rate)) {
            $rate = 1;
        }

        return $rate;
    }

    /**
     * Get current currency code
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCurrencyCode() {
        return $this->_storeManager->getStore()->getCurrentCurrency()->getCode();
    }

    /**
     * Check if there is any product rule active
     *
     * @return bool
     */
    public function isSetProductRule() {
        $productRuleCollection = $this->ruleCollectionFactory->create()->addFieldToFilter(['rule_type', 'status'], [1, 1])->getData();
        if (empty($productRuleCollection)) return false;
        return true;
    }

    /**
     * Get point range for bundle product
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBundleProductPointsRange() {
        $product = $this->_coreRegistry->registry('current_product');
        return $this->_helper->getBundleProductPointsRange($product);
    }

    public function getProductRuleByModel($product) {
        return $this->_helper->getProductRuleByModel($product);
    }


    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities= [];

        $rules = $this->getValidProductRule();
        if (!empty($rules)) {
            foreach ($rules as $rule) {
                $identities = array_merge($identities, $rule->getIdentities());
            }
        }

        if ($this->_coreRegistry->registry('product')) {
            return $identities;
        }

        $products = $this->getLoadedProductCollection();
        foreach ($products as $product) {
            $identities = array_merge($identities, $product->getIdentities());
        }

        return $identities;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getLoadedProductCollection() {
        return $this->listProduct->getLoadedProductCollection();
    }

    /**
     * Get valid product rule
     *
     * @return array|null
     */
    public function getValidProductRule() {
        if ($this->validProductRules === null)
            $this->validProductRules = $this->_helper->getValidProductRule();

        return $this->validProductRules;
    }

    /**
     * Get product rules for product in category page
     *
     * @return false|string
     */
    public function getProductRules() {
        $item = $this->_coreRegistry->registry('product');
        if ($item === null) {
            $items = $this->getLoadedProductCollection()->getItems();

            $productRules = [];
            foreach ($items as $item) {
                $itemId = $item->getEntityId();
                $productRules[$itemId] = $this->getProductRuleByModel($item);
            }
        } else {
            $productRules = $this->getProductRuleByModel($item);
        }

        return json_encode($productRules);
    }

}
