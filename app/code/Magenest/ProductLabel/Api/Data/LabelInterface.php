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

namespace Magenest\ProductLabel\Api\Data;

/**
 * Label interface
 *
 * @api
 */
interface LabelInterface
{

    /**#@+
     * Constants of table name
     */
    const TABLE_NAME = 'magenest_product_label';

    /**#@+
     * Constants defined for keys of data array
     */
    const LABEL_ID = 'label_id';
    const NAME = 'name';
    const STATUS = 'status';
    const TYPE = 'type';
    const CONDITION = 'conditions_serialized';
    const PRIORITY = 'priority';
    const FROM_DATE = 'from_date';
    const TO_DATE = 'to_date';
    const LABEL_TYPE = 'label_type';
    const DEFAULT_TYPE = 'default_type';


    /**#@+
     * Constants of label Type
     */
    const NEW_LABEL = 'New Label';
    const SALE_LABEL = 'New Sale';
    const BEST_SELLER_LABEL = 'Best Seller Label';

    /**#@+
     * Constants of image default
     */
    const IMAGE_LABEL_NEW = 'Temp_new_category.png';
    const IMAGE_LABEL_SALE = 'Temp_sale_category.png';
    const IMAGE_LABEL_BEST_SELLER = 'Temp_best_seller_category.png';

    const IMAGE_NEW_PRODUCT = 'Temp_new_product.png';
    const IMAGE_SALE_PRODUCT = 'Temp_sale_product.png';
    const IMAGE_BEST_SELLER_PRODUCT = 'Temp_best_seller_product.png';

    /**#@+
     * Constants of data default
     */
    const POSITION_DEFAULT = 'top-left';
    const TYPE_DEFAULT = '3';
    const LABEL_SIZE_DEFAULT = '100%';
    const USE_DEFAULT = '0';
    const TEXT_SIZE_DEFAULT = '16';

    /**
     * Get Product Label Id
     *
     * @return int
     */
    public function getId();

    /**
     * Set Product Label Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get Product Label Name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set Product Label Name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get Product Label Status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Set Product Label Status
     *
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * Get Product Label Type (text, shape, image)
     *
     * @return int|null
     */
    public function getType();

    /**
     * Set Product Label Type
     *
     * @param int $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get Rule Condition
     *
     * @return string|null
     */
    public function getCondition();

    /**
     * Set Rule Condition
     *
     * @param string $condition
     * @return $this
     */
    public function setCondition($condition);

    /**
     * Get Product Label Priority
     *
     * @return int|null
     */
    public function getPriority();

    /**
     * Set Product Label Priority
     *
     * @param int $priority
     * @return $this
     */
    public function setPriority($priority);

    /**
     * Get Product Label From Date
     *
     * @return string|null
     */
    public function getFromDate();

    /**
     * Set product label from date
     *
     * @param $fromDate
     * @return $this
     */
    public function setFromDate($fromDate);

    /**
     * Get product label to date
     *
     * @return string|null
     */
    public function getToDate();

    /**
     * Set product label to date
     *
     * @param $toDate
     * @return $this
     */
    public function setToDate($toDate);

}
