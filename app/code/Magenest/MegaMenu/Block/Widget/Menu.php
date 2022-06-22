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

namespace Magenest\MegaMenu\Block\Widget;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class Menu
 * @package Magenest\MegaMenu\Block\Widget
 */
class Menu extends Template implements BlockInterface
{
	protected $_template = 'Magenest_MegaMenu::html/topmenu.phtml';
}
