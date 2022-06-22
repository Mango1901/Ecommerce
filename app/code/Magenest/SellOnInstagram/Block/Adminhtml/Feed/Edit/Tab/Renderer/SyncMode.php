<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\Renderer;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class SyncMode extends AbstractRenderer
{
    const COLUMN_NAME = 'user_id';
    const USER_ID_FOR_SCHEDULE_MODE = 0;

    public function render(DataObject $row)
    {
        $html = '';
        $userId = $row->getData(self::COLUMN_NAME);
        if (isset($userId)) {
            if ($userId == self::USER_ID_FOR_SCHEDULE_MODE) {
                $html = "<span>By Schedule</span>";
            } else {
                $html = "<span>Manually</span>";
            }
        }
        return $html;
    }
}
