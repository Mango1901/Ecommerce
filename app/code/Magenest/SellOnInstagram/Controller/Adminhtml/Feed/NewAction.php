<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;

class NewAction extends AbstractFeed
{
    public function execute()
    {
        $this->_forward('edit');
    }
}
