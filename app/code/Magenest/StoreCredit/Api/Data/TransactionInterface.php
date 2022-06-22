<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magenest.com license that is
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

namespace Magenest\StoreCredit\Api\Data;

/**
 * Interface TransactionInterface
 * @package Magenest\StoreCredit\Api\Data
 */
interface TransactionInterface
{
    const TRANSACTION_ID = 'transaction_id';
    const CUSTOMER_ID    = 'customer_id';
    const ORDER_ID       = 'order_id';
    const TITLE          = 'title';
    const STATUS         = 'status';
    const ACTION         = 'action';
    const AMOUNT         = 'amount';
    const BALANCE        = 'balance';
    const CUSTOMER_NOTE  = 'customer_note';
    const ADMIN_NOTE     = 'admin_note';
    const CREATED_AT     = 'created_at';

    /**
     * @return int
     */
    public function getTransactionId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setTransactionId($value);

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
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setOrderId($value);

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTitle($value);

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
    public function getAction();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAction($value);

    /**
     * @return string
     */
    public function getAmount();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setAmount($value);

    /**
     * @return string
     */
    public function getBalance();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setBalance($value);

    /**
     * @return string
     */
    public function getCustomerNote();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerNote($value);

    /**
     * @return string
     */
    public function getAdminNote();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAdminNote($value);

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
}
