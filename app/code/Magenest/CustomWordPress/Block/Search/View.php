<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_tn233 extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_tn233
 */

namespace Magenest\CustomWordPress\Block\Search;

use FishPig\WordPress\Model\Context as WPContext;
use Magenest\CustomWordPress\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class View extends \FishPig\WordPress\Block\Search\View
{
    /**
     * @var Data
     */
    private $hlp;

    /**
     * View constructor.
     * @param Context $context
     * @param WPContext $wpContext
     * @param Data $hlp
     * @param array $data
     */
    public function __construct(
        Context $context,
        WPContext $wpContext,
        Data $hlp,
        array $data = []
    ) {
        $this->hlp = $hlp;
        parent::__construct($context, $wpContext, $data);
    }

    public function getFormActionUrl()
    {
        return $this->url->getUrl('search') . '/';
    }

    protected function _beforeToHtml()
    {
        $this->setTemplate('FishPig_WordPress::post/list/wrapper.phtml');

        return parent::_beforeToHtml();
    }
}
