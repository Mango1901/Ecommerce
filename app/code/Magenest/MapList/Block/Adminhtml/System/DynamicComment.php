<?php

namespace Magenest\MapList\Block\Adminhtml\System;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Config\Model\Config\CommentInterface;

class DynamicComment extends AbstractBlock implements CommentInterface
{
    public function getCommentText($elementValue)
    {
        $url = $this->_urlBuilder->getUrl('adminhtml/system_config/edit/section/maplist');
        return "<a href='$url'>Goong Maptiles Key</a> and Goong API Key must enabled in your Goong key to support this feature.";
    }
}
