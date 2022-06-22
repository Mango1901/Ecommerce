<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Api\Data\Store;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ContactInterface
 * @package Magenest\MobileApi\Api\Data\Store
 */
interface MediaEntryInterface extends ExtensibleDataInterface
{
    /**
     * Get field id
     *
     * @return string
     * @since 102.0.0
     */
    public function getFieldId();

    /**
     * Set field id
     *
     * @param string $fieldId
     * @return $this
     */
    public function setFieldId($fieldId);

    /**
     * Get name
     *
     * @return string
     * @since 102.0.0
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get type
     *
     * @return string
     * @since 102.0.0
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get content
     *
     * @return string
     * @since 102.0.0
     */
    public function getContent();

    /**
     * Set content
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content);
}
