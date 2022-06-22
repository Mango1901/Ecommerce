<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Store;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Store\ContactInterface;

/**
 * Class Contact
 * @package Magenest\MobileApi\Model\Catalog
 */
class Contact extends AbstractSimpleObject implements ContactInterface
{
    /** @const */
    const KEY_NAME = 'name';
    const KEY_TYPE = 'type';
    const KEY_EMAIL = 'email';
    const KEY_TELEPHONE = 'telephone';
    const KEY_ADDRESS = 'address';
    const KEY_TOPIC = 'topic';
    const KEY_cOMMENT = 'comment';

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_get(self::KEY_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_get(self::KEY_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(self::KEY_TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmail()
    {
        return $this->_get(self::KEY_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmail($email)
    {
        return $this->setData(self::KEY_EMAIL, $email);
    }

    /**
     * {@inheritdoc}
     */
    public function getTelephone()
    {
        return $this->_get(self::KEY_TELEPHONE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTelephone($telephone)
    {
        return $this->setData(self::KEY_TELEPHONE, $telephone);
    }

    /**
     * {@inheritdoc}
     */
    public function getAddress()
    {
        return $this->_get(self::KEY_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($address)
    {
        return $this->setData(self::KEY_ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getTopic()
    {
        return $this->_get(self::KEY_TOPIC);
    }

    /**
     * {@inheritdoc}
     */
    public function setTopic($topic)
    {
        return $this->setData(self::KEY_TOPIC, $topic);
    }

    /**
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->_get(self::KEY_cOMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        return $this->setData(self::KEY_cOMMENT, $comment);
    }
}
