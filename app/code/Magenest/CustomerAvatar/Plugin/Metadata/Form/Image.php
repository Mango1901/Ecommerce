<?php

namespace Magenest\CustomerAvatar\Plugin\Metadata\Form;

use Magento\Framework\Api\Data\ImageContentInterface;

/**
 * Class Image
 * @package Magenest\CustomerAvatar\Plugin\Metadata\Form
 */
class Image
{
    /**
     * @var \Magenest\CustomerAvatar\Model\Source\Validation\Image
     */
    protected $validImage;

    /**
     * Image constructor.
     * @param \Magenest\CustomerAvatar\Model\Source\Validation\Image $validImage
     */
    public function __construct(
        \Magenest\CustomerAvatar\Model\Source\Validation\Image $validImage
    ) {
        $this->validImage = $validImage;
    }

    /**
     * {@inheritdoc}
     *
     * @return ImageContentInterface|array|string|null
     */
    public function beforeExtractValue(\Magento\Customer\Model\Metadata\Form\Image $subject, $value)
    {
        $attrCode = $subject->getAttribute()->getAttributeCode();

        if (!$this->validImage->isImageValid('tmp_name', $attrCode)) {
            $_FILES[$attrCode]['tmp_name'] = $_FILES[$attrCode]['tmp_name'];
            unset($_FILES[$attrCode]['tmp_name']);
        }

        return [$value];
    }
}
