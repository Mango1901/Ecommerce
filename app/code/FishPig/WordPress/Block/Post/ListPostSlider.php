<?php
/**
 * @category FishPig
 * @package  FishPig_WordPress
 * @author   Ben Tideswell <help@fishpig.co.uk>
 */
namespace FishPig\WordPress\Block\Post;

use FishPig\WordPress\Block\Post;
use FishPig\WordPress\Model\ResourceModel\Post\Collection as PostCollection;

class ListPostSlider extends Post
{
    /**
     * Cache for post collection
     *
     * @var PostCollection
     */
    protected $_postCollection = null;

    /**
     * Returns the collection of posts
     *
     * @return false|PostCollection|object
     */
    public function getPosts()
    {
        if ($this->_postCollection === null) {
            $this->_postCollection = $this->factory->create(PostCollection::class);
            $this->_postCollection->addIsViewableFilter()->isVisibleSliderFilter();

            $mode = $this->getRequest()->getParam('mode');

            if ($mode === 'featured_last_week') {
                $this->_postCollection->addFieldToFilter('visible_most_viewed', 1);
            } else {
                $this->_postCollection->setOrderByPostDate();
            }
        }

        return $this->_postCollection;
    }
}
