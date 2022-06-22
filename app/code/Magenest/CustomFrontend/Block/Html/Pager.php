<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\CustomFrontend\Block\Html;

/**
 * Html pager block
 *
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 * @api
 * @since 100.0.2
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * The list of available pager limits
     *
     * @var array
     */
    protected $_availableLimit = [8 => 8, 16 => 16, 32 => 32];
}
