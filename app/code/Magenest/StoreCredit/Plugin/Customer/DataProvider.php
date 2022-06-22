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

namespace Magenest\StoreCredit\Plugin\Customer;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\StoreCredit\Helper\Data;
use Magenest\StoreCredit\Model\CustomerFactory;

/**
 * Class DataProvider
 * @package Magenest\StoreCredit\Plugin\Customer
 */
class DataProvider
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param Data $helper
     * @param StoreManagerInterface $storeManager
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        Data $helper,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory
    ) {
        $this->helper = $helper;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Customer\Ui\Component\DataProvider $subject
     * @param array $result
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function afterGetData(\Magento\Customer\Ui\Component\DataProvider $subject, $result)
    {
        if (isset($result['items'])) {
            $data = $this->customerFactory->create()->attachDataToCustomerGrid($result['items']);

            foreach ($result['items'] as &$item) {
                if (isset($data[$item['entity_id']])) {
                    foreach ((array)$data[$item['entity_id']] as $datum) {
                        foreach ($datum as $key => $value) {
                            if ($key == 'mp_credit_balance') {
                                /** @var Store $store */
                                $store = $this->storeManager->getStore($datum['store_id']);
                                $value = $this->helper->formatPrice($value, null, null, $store->getBaseCurrency());
                            }

                            $item[$key] = $value;
                        }
                    }
                }
            }
        }

        return $result;
    }
}
