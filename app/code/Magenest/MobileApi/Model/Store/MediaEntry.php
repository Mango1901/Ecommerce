<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Store;

use Magento\Framework\Api\AbstractSimpleObject;
use Magenest\MobileApi\Api\Data\Store\MediaEntryInterface;

/**
 * Class MediaEntry
 * @package Magenest\MobileApi\Model\Catalog
 */
class MediaEntry extends AbstractSimpleObject implements MediaEntryInterface
{
    /** @const */
    const KEY_FIELD_ID = 'fieldId';
    const KEY_NAME = 'name';
    const KEY_TYPE = 'type';
    const KEY_CONTENT = 'content';

    /**
     * {@inheritdoc}
     */
    public function getFieldId()
    {
        return $this->_get(self::KEY_FIELD_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setFieldId($fieldId)
    {
        return $this->setData(self::KEY_FIELD_ID, $fieldId);
    }

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
    public function getContent()
    {
        return $this->_get(self::KEY_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($content)
    {
        return $this->setData(self::KEY_CONTENT, $content);
    }
}