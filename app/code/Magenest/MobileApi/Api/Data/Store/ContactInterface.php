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
interface ContactInterface extends ExtensibleDataInterface
{
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
     * Get name
     *
     * @return string
     * @since 102.0.0
     */
    public function getType();

    /**
     * Set name
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get email
     *
     * @return string
     * @since 102.0.0
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get telephone
     *
     * @return string
     * @since 102.0.0
     */
    public function getTelephone();

    /**
     * Set telephone
     *
     * @param string $telephone
     * @return $this
     */
    public function setTelephone($telephone);

    /**
     * Get address
     *
     * @return string
     * @since 102.0.0
     */
    public function getAddress();

    /**
     * Set address
     *
     * @param string $address
     * @return $this
     */
    public function setAddress($address);

    /**
     * Get topic
     *
     * @return string
     * @since 102.0.0
     */
    public function getTopic();

    /**
     * Set topic
     *
     * @param string $topic
     * @return $this
     */
    public function setTopic($topic);

    /**
     * Get comment
     *
     * @return string
     * @since 102.0.0
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return $this
     */
    public function setComment($comment);
}
