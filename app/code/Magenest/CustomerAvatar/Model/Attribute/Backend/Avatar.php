<?php

namespace Magenest\CustomerAvatar\Model\Attribute\Backend;

use Magenest\CustomerAvatar\Model\Source\Validation\Image;
use Magenest\CustomerAvatar\Model\Source\Validation\ImageFactory;
use Magenest\CustomerAvatar\Setup\Patch\Data\ProfilePictureAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Avatar
 * @package Magenest\CustomerAvatar\Model\Attribute\Backend
 */
class Avatar extends AbstractBackend
{
    /**
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @param ImageFactory $imageFactory
     */
    public function __construct(
        ImageFactory $imageFactory
    ) {
        $this->imageFactory = $imageFactory;
    }

    /**
     * @param DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        /**
         * @var $validation Image
         */
        $validation = $this->imageFactory->create();
        $attrCode = $this->getAttribute()->getAttributeCode();
        if ($attrCode == ProfilePictureAttribute::PROFILE_PICTURE_CODE) {
            if ($validation->isImageValid('tmpp_name', $attrCode) === false) {
                throw new LocalizedException(
                    __('The profile picture is not a valid image.')
                );
            }
        }

        return parent::beforeSave($object);
    }
}
