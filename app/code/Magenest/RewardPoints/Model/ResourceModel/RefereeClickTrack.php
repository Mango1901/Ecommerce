<?php

namespace Magenest\RewardPoints\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RefereeClickTrack extends AbstractDb
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'referee_click_track_resource_model';

    /**
     * Initialize resource model.
     */
    protected function _construct()
    {
        $this->_init('referee_click_track', 'entity_id');
        $this->_useIsObjectNew = true;
    }
}
