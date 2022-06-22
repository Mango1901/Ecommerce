<?php

namespace Magenest\CustomWordPress\Block\Homepage;

use FishPig\WordPress\Model\Context as WPContext;
use Magenest\CustomWordPress\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class View
 * @package Magenest\CustomWordPress\Block\Homepage
 */
class View extends \FishPig\WordPress\Block\Homepage\View
{
    protected $hlp;

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

    public function getCategories()
    {
        return $this->hlp->getCategories();
    }

    public function getCategoryId()
    {
        return $this->getRequest()->getParam('cat') ?: $this->hlp->getFirstCategoryId();
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
