<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/10/16
 * Time: 09:48
 */

namespace Magenest\MapList\Model\ModelInterface;

interface LocationInterface
{
    const ID = 'location_id';
    const TITLE = 'title';
    const DESCRIPTION = 'description';
    const LATIDUDE = 'latitude';
    const LONGITUDE = 'longitude';
    const SHORT_DESCRIPTION = 'short_description';
    const ADDRESS = 'address';
    const WEBSITE = 'website';
    const SMALL_IMAGE = 'small_image';
    const BIG_IMAGE = 'big_image';
    const IS_ACTIVE = 'is_active';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PHONE_NUMBER = 'phone_number';
    const EMAIL = 'email';

    public function getId();

    public function getTitle();

    public function getDescription();

    public function getLatitude();

    public function getLongitude();

    public function getShortDescription();

    public function getAddress();

    public function getWebsite();

    public function getSmallImage();

    public function getBigImage();

    public function isActive();

    public function getCreatedAt();

    public function getUpdatedAt();

    public function getPhoneNumber();

    public function getEmail();

    public function setId($id);

    public function setTitle($title);

    public function setDescription($description);

    public function setLatitude($latitude);

    public function setLongitude($longitude);

    public function setShortDescription($shortDescription);

    public function setAddress($address);

    public function setWebsite($website);

    public function setSmallImage($image_url);

    public function setBigImage($image_url);

    public function setIsActive($isActive);

    public function setCreatedAt($unixTime);

    public function setUpdatedAt($unixTime);

    public function setPhoneNumber($phoneNumber);

    public function setEmail($email);
}
