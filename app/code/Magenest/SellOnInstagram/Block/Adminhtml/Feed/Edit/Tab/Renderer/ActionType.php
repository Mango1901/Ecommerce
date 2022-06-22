<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\Renderer;

use Magento\Framework\DataObject;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

class ActionType extends AbstractRenderer
{
    const COLUMN_NAME = 'action';
    const ACTION_CREATE = 1;

    public function render(DataObject $row)
    {
        $html = '';
        $userId = $row->getData(self::COLUMN_NAME);
        if (isset($userId)) {
            if ($userId == self::ACTION_CREATE) {
                $html = "<span>Create & Update</span>";
            } else {
                $html = "<span>Delete</span>";
            }
        }
        return $html;
    }
}
