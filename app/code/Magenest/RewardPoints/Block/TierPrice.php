<?php

namespace Magenest\RewardPoints\Block;

use Magento\Customer\Model\Session;

/**
 * Class TierPrice
 * @package Magenest\RewardPoints\Block
 */
class TierPrice extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $_serialize;

    /**
     * @var \Magenest\RewardPoints\Model\AccountFactory
     */
    protected $accountFactory;

    /**
     * TierPrice constructor.
     * @param Session $customerSession
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serialize
     * @param \Magenest\RewardPoints\Model\AccountFactory $accountFactory
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize,
        \Magenest\RewardPoints\Model\AccountFactory $accountFactory
    ) {
        $this->_customerSession = $customerSession;
        $this->_helper          = $helper;
        $this->_serialize        = $serialize;
        $this->accountFactory = $accountFactory;
        parent::__construct($context);
    }

    /**
     * @return Session
     */
    public function getCustomer()
    {
        return $this->_customerSession;
    }

    /**
     * @param $productPrice
     *
     * @return float
     */
    public function getDiscountPrice($productPrice)
    {
        $discountPercent = $this->getDiscountPercent();
        $discountPrice   = (float)($productPrice - $productPrice * $discountPercent / 100);

        return $discountPrice;
    }

    /**
     * @return mixed
     */
    public function getDiscountInfo()
    {
        $checkData = $this->_helper->getDiscountInfo();
        if (isset($checkData)) {
            return $this->_serialize->unserialize($checkData);
        } else {
            return null;
        }
    }

    /**
     * @return int
     */
    public function getDiscountPercent()
    {
        $customerPoints = $this->getCustomerPoints($this->_customerSession->getId());

        return $this->_helper->getDiscountPercent($customerPoints);
    }

    /**
     * @param $customerId
     *
     * @return mixed
     */
    public function getCustomerPoints($customerId)
    {
        $accountModel = $this->accountFactory->create()->load($customerId, 'customer_id');
        return $accountModel->getData('points_current');
    }
}
