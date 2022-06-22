<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Block\Account\Navigation;

use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Html\Link\Current;
use Magento\Framework\View\Element\Template\Context;
use Magenest\StoreCredit\Helper\Data;

/**
 * Class Setting
 * @package Magenest\StoreCredit\Block\Account
 */
class Setting extends Current
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Setting constructor.
     *
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        DefaultPathInterface $defaultPath,
        Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (!$this->helper->isEnabled() ||
            !$this->helper->isEnabledForCustomer() ||
            $this->helper->isAffiliateAccount()
        ) {
            return '';
        }

        return parent::_toHtml();
    }
}
