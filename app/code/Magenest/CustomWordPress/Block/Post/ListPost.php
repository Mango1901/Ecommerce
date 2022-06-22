<?php

namespace Magenest\CustomWordPress\Block\Post;

use FishPig\WordPress\Model\Context as WPContext;
use FishPig\WordPress\Model\ResourceModel\Post\Collection;
use Magenest\CustomWordPress\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class ListPost
 * @package Magenest\CustomWordPress\Block\Post
 */
class ListPost extends \FishPig\WordPress\Block\Post\ListPost
{
    protected $hlp;

    /**
     * ListPost constructor.
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

    /**
     * @return bool|Collection|mixed
     */
    public function getPosts()
    {
        if ($this->_postCollection === null) {
            if ($this->getWrapperBlock()) {
                if ($this->_postCollection = $this->getWrapperBlock()->getPostCollection()) {
                    if ($this->getPostType()) {
                        $this->_postCollection->addPostTypeFilter($this->getPostType());
                    }
                }
            } else {
                $this->_postCollection = $this->factory->create('FishPig\WordPress\Model\ResourceModel\Post\Collection');
            }

            if ($this->_postCollection && ($pager = $this->getChildBlock('pager'))) {
                $pager->setPostListBlock($this)->setCollection($this->_postCollection);
            }
        }
        $cat = $this->getCategoryId();
        if ($cat) {
            $this->_postCollection->addCategoryIdFilter($cat);
        }
        $this->_postCollection->setPageSize(10);
        return $this->_postCollection;
    }

    public function getCategoryId()
    {
        return $this->getRequest()->getParam('cat') ?: $this->hlp->getFirstCategoryId();
    }

    protected function _beforeToHtml()
    {
        $this->setTemplate('FishPig_WordPress::post/list.phtml');

        return parent::_beforeToHtml();
    }
}
