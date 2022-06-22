<?php

namespace Magenest\CustomFrontend\Block\Html;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class OpenGraph
 * Used for block open graph head tag
 */
class OpenGraph extends Template
{
    const META_OG_IMAGE_DEFAULT = 'theme/meta/meta-image.jpeg';

    /**
     * @return string
     */
    public function getMetaImageUrl()
    {
        try {
            $mediaPath = $this->_storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);
            return $mediaPath . self::META_OG_IMAGE_DEFAULT;
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }
}
