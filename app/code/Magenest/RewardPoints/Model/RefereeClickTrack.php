<?php

namespace Magenest\RewardPoints\Model;

use Magenest\RewardPoints\Model\ResourceModel\RefereeClickTrack as ResourceModel;
use Magento\Framework\Model\AbstractModel;

class RefereeClickTrack extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'referee_click_track_model';

    /**
     * Initialize magento model.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }
}
