<?php

namespace Magenest\CustomerAvatar\Model\Source\Validation;

use Magenest\CustomerAvatar\Setup\Patch\Data\ProfilePictureAttribute;

/**
 * Class Image
 */
class Image
{
    /**
     * Check the image file
     * @param $tmp_name, $attrCode
     * @return bool
     */
    public function isImageValid($tmp_name, $attrCode)
    {
        if ($attrCode == ProfilePictureAttribute::PROFILE_PICTURE_CODE) {
            if (!empty($_FILES[$attrCode][$tmp_name])) {
                $imageFile = @getimagesize($_FILES[$attrCode][$tmp_name]);
                if ($imageFile === false) {
                    return false;
                } else {
                    $valid_types = ['image/gif', 'image/jpeg', 'image/png'];
                    if (!in_array($imageFile['mime'], $valid_types)) {
                        return false;
                    }
                }
                return true;
            }
        }
        return true;
    }
}
