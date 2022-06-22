<?php

namespace Magenest\AffiliateOpt\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface TransactionInterface
 * @api
 */
interface TransactionInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const TRANSACTION_ID = 'transaction_id';

    const ACCOUNT_ID = 'account_id';

    const AMOUNT = 'amount';

    const TITLE = 'title';

    const HOLDING_TO = 'holding_to';

    const CUSTOMER_ID = 'customer_id';

    const ACTION = 'action';

    const TYPE = 'type';

    const AMOUNT_USED = 'amount_used';

    const CURRENT_BALANCE = 'current_balance';

    const STATUS = 'status';

    const ORDER_ID = 'order_id';

    const ORDER_INCREMENT_ID = 'order_increment_id';

    const STORE_ID = 'store_id';

    const CAMPAIGN_ID = 'campaign_id';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    const EXTRA_CONTENT = 'extra_content';

    /**#@-*/

    /**
     * @return int transaction id.
     */
    public function getTransactionId();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setTransactionId($id);

    /**
     * @return int
     */
    public function getAccountId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setAccountId($id);

    /**
     * @return float
     */
    public function getAmount();

    /**
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount($amount);

    /**
     * @return string.
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string.
     */
    public function getHoldingTo();

    /**
     * @param int $data
     *
     * @return $this
     */
    public function setHoldingTo($data);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAction($value);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setType($value);

    /**
     * @return float
     */
    public function getAmountUsed();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setAmountUsed($value);

    /**
     * @return float
     */
    public function getCurrentBalance();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setCurrentBalance($value);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * @return string
     */
    public function getOrderId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @return string
     */
    public function getOrderIncrementId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOrderIncrementId($value);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @return string
     */
    public function getCampaignId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCampaignId($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdatedAt($value);

    /**
     * @return string
     */
    public function getExtraContent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setExtraContent($value);

    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magenest\AffiliateOpt\Api\Data\TransactionExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Magenest\AffiliateOpt\Api\Data\TransactionExtensionInterface $extensionAttributes
     *
     * @return $this
     */
    public function setExtensionAttributes(
        \Magenest\AffiliateOpt\Api\Data\TransactionExtensionInterface $extensionAttributes
    );
}
