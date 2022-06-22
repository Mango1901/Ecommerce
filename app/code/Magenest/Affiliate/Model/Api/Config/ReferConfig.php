<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\ReferConfigInterface;

/**
 * Class ReferConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class ReferConfig extends \Magento\Framework\DataObject implements ReferConfigInterface
{
    /**
     * {@inheritdoc}
     */
    public function getEnable()
    {
        return $this->getData(self::ENABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setEnable($value)
    {
        return $this->setData(self::ENABLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAccountSharing()
    {
        return $this->getData(self::ACCOUNT_SHARING);
    }

    /**
     * {@inheritdoc}
     */
    public function setAccountSharing($value)
    {
        return $this->setData(self::ACCOUNT_SHARING, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultLink()
    {
        return $this->getData(self::DEFAULT_LINK);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultLink($value)
    {
        return $this->setData(self::DEFAULT_LINK, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSharingContent()
    {
        return $this->getData(self::SHARING_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSharingContent($value)
    {
        return $this->setData(self::SHARING_CONTENT, $value);
    }
}
