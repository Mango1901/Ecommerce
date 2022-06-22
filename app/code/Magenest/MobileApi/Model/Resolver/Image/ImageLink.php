<?php

namespace Magenest\MobileApi\Model\Resolver\Image;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\UrlInterface;

/**
 * Returns product's image data
 */
class ImageLink implements ResolverInterface
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(UrlInterface $urlBuilder)
    {
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $mediaUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        if (isset($value[$field->getName()])) {
            $isMatch = preg_match('/{{media url=(.*)}}/', $value[$field->getName()], $matches);
            return $isMatch ? $mediaUrl . end($matches) : null;
        }
        return null;
    }
}
