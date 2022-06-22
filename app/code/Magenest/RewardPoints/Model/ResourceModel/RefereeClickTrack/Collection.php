<?php

namespace Magenest\RewardPoints\Model\ResourceModel\RefereeClickTrack;

use Magenest\RewardPoints\Model\RefereeClickTrack as Model;
use Magenest\RewardPoints\Model\ResourceModel\RefereeClickTrack as ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'referee_click_track_collection';

    /**
     * Initialize collection model.
     */
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
