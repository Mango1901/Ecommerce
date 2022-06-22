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

namespace Magenest\StoreCredit\Model;

use Exception;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magenest\StoreCredit\Api\Data\StoreCreditCustomerExtensionInterface;
use Magenest\StoreCredit\Api\Data\StoreCreditCustomerInterface;

/**
 * Class Customer
 * @package Magenest\StoreCredit\Model
 */
class Customer extends AbstractModel implements IdentityInterface, StoreCreditCustomerInterface
{
    /**
     * Cache tag
     */
    const CACHE_TAG = 'magenest_store_credit_customer';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_store_credit_customer';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magenest\StoreCredit\Model\ResourceModel\Customer');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @param AbstractModel $model
     *
     * @return $this
     */
    public function attachAttributeData(AbstractModel $model)
    {
        $model->addData($this->getData());

        return $this;
    }

    /**
     * @param $objId
     * @param $data
     *
     * @return $this
     * @throws Exception
     */
    public function saveAttributeData($objId, $data)
    {
        $this->addData($data)->setId($objId)->save();

        return $this;
    }

    /**
     * @param array $entities
     *
     * @return array
     */
    public function attachDataToCustomerGrid($entities)
    {
        return $this->getResource()->attachDataToCustomerGrid($entities);
    }

    /**
     * @return AbstractDb|ResourceModel\Customer
     */
    public function getResource()
    {
        return parent::getResource();
    }

    /**
     * @param $customerId
     *
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        return $this->load($customerId, 'customer_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($value)
    {
        return $this->setData(self::CUSTOMER_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMpCreditBalance()
    {
        return $this->getData(self::STORE_CREDIT_BALANCE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMpCreditBalance($value)
    {
        return $this->setData(self::STORE_CREDIT_BALANCE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMpCreditNotification()
    {
        return $this->getData(self::STORE_CREDIT_NOTIFICATION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMpCreditNotification($value)
    {
        return $this->setData(self::STORE_CREDIT_NOTIFICATION, $value);
    }
}
