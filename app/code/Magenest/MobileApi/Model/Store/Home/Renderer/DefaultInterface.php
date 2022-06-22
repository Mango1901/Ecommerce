<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Store\Home\Renderer;

use Magenest\MobileApi\Api\Data\Store\HomeExtension;

/**
 * Interface DefaultInterface
 * @package Magenest\MobileApi\Model\Store\Home\Renderer
 */
interface DefaultInterface
{
    /**
     * Process home store
     *
     * @param HomeExtension $extension
     */
    public function process(HomeExtension $extension);
}