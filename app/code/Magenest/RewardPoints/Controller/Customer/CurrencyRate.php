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
 * Class CurrencyRate
 * @package Magenest\RewardPoints\Controller\Customer
 */
class CurrencyRate extends Action
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
     * CurrencyRate constructor.
     *
     * @param Context $context
     * @param StoreManagerInterface $storeManager
     * @param JsonFactory $jsonHelper
     * @param ProductFactory $productFactory
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        JsonFactory $jsonHelper,
        ProductFactory $productFactory,
        Data $helper
    )
    {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->productFactory = $productFactory;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $currency = $this->getRequest()->getParam('currency');
        $id = $this->getRequest()->getParam('id');

        if (isset($currency) && !isset($id)) {
            $rate = $this->storeManager->getStore()->getBaseCurrency()->getRate($currency);
            if (!isset($rate)) {
                $rate = 1;
            }
            $result = $rate;
        }
        else if (isset($id))
        {
            $result = $this->helper->getProductRule($id);
        }

        $resultJson = $this->jsonHelper->create();
        return $resultJson->setData($result);
    }
}
