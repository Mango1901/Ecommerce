<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 10:15
 */

namespace Magenest\MapList\Model;

use Magenest\MapList\Model\ModelInterface\LocationInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Magenest\MapList\Helper\Constant;

class Location extends AbstractModel implements LocationInterface, IdentityInterface
{
    /**
     * CMS page cache tag
     */
    const CACHE_TAG = "magenest_maplist_location";

    /**
     * @var string
     */
    protected $_cacheTag = 'magenest_maplist_location';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_maplist_location';

    protected $_idFieldName = 'location_id';

    protected function _construct()
    {
        $this->_init(Constant::LOCATION_RESOURCE_MODEL);
    }

    //Implement Getter/Setter function
    public function getId()
    {
        return $this->getData(self::ID);
    }

    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Get content
     *
     * @return string|null
     */

    public function isActive()
    {
        return (bool)$this->getData(self::IS_ACTIVE);
    }

    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    public function setTitle($title)
    {
        return $this->setData(self::TITLE, $title);
    }

    public function setIsActive($is_active)
    {
        return $this->setData(self::IS_ACTIVE, $is_active);
    }

    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    public function getLatitude()
    {
        return $this->getData(self::LATIDUDE);
    }

    public function getLongitude()
    {
        return $this->getData(self::LONGITUDE);
    }

    public function getAddress()
    {
        return $this->getData(self::ADDRESS);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setDescription($description)
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    public function setLatitude($latitude)
    {
        return $this->setData(self::LATIDUDE, $latitude);
    }

    public function setLongitude($longitude)
    {
        return $this->setData(self::LONGITUDE, $longitude);
    }

    public function setAddress($address)
    {
        return $this->setData(self::ADDRESS, $address);
    }

    public function setCreatedAt($unixTime)
    {
        return $this->setData(self::CREATED_AT, $unixTime);
    }

    public function setUpdatedAt($unixTime)
    {
        return $this->setData(self::UPDATED_AT, $unixTime);
    }

    public function getIdentities()
    {
        return array(self::CACHE_TAG . '_' . $this->getId());
    }

    public function getShortDescription()
    {
        return $this->getData(self::SHORT_DESCRIPTION);
    }

    public function setShortDescription($shortDescription)
    {
        return $this->setData(self::SHORT_DESCRIPTION, $shortDescription);
    }

    public function getWebsite()
    {
        return $this->getData(self::WEBSITE);
    }

    public function getSmallImage()
    {
        return $this->getData(self::SMALL_IMAGE);
    }

    public function getBigImage()
    {
        return $this->getData(self::BIG_IMAGE);
    }

    public function setWebsite($website)
    {
        return $this->setData(self::WEBSITE, $website);
    }

    public function setSmallImage($image_url)
    {
        return $this->setData(self::SMALL_IMAGE, $image_url);
    }

    public function setBigImage($image_url)
    {
        return $this->setData(self::BIG_IMAGE, $image_url);
    }

    public function getPhoneNumber()
    {
        return $this->getData(self::PHONE_NUMBER);
    }

    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    public function setPhoneNumber($phoneNumber)
    {
        return $this->setData(self::PHONE_NUMBER, $phoneNumber);
    }

    public function setEmail($email)
    {
        return $this->getData(self::EMAIL, $email);
    }
}
