<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Block\Menu\Component;

class Background extends \Magento\Framework\View\Element\Template implements \Magento\Framework\View\Element\BlockInterface
{
    protected $_template = 'Magenest_MegaMenu::menu/component/background.phtml';

    public function isShow()
    {
        return $this->getData('image') || $this->getData('color');
    }

    public function getOpacity()
    {
        if (!$this->getData('opacity')) {
            return 1;
        }

        return $this->getData('opacity');
    }

    public function getBackgroundSize()
    {
        $width = $this->getWidth();
        $height = $this->getHeight();

        if ($width === 'auto' && $height === 'auto') {
            return 'cover';
        }

        return $width . ' ' . $height;
    }

    public function getWidth()
    {
        $v = $this->getData('width');
        if (is_numeric($v)) {
            return $v . 'px';
        } elseif (is_string($v)) {
            return $v;
        }

        return 'auto';
    }

    public function getHeight()
    {
        $v = $this->getData('height');
        if (is_numeric($v)) {
            return $v . 'px';
        } elseif (is_string($v)) {
            return $v;
        }

        return 'auto';
    }

    public function getImgUrl()
    {
        $img = $this->getData('image');
        if (is_array($img) && isset($img['url'])) {
            return $img['url'];
        } elseif (is_string($img)) {
            return $this->_storeManager->getStore()->getBaseUrl(
                    \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                ) . 'mega_menu/item/' . $img;
        }

        return '';
    }
}
