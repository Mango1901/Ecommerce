<?php


namespace Magenest\Affiliate\Model\Api\Config;

use Magenest\Affiliate\Api\Data\Config\ReferSharingContentInterface;

/**
 * Class ReferSharingContentConfig
 * @package Magenest\Affiliate\Model\Api\Config
 */
class ReferSharingContentConfig extends \Magento\Framework\DataObject implements ReferSharingContentInterface
{
    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->getData(self::SUBJECT);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($value)
    {
        return $this->setData(self::SUBJECT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEmailContent()
    {
        return $this->getData(self::EMAIL_CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setEmailContent($value)
    {
        return $this->setData(self::EMAIL_CONTENT, $value);
    }
}
