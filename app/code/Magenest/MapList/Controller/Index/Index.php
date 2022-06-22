<?php

namespace Magenest\MapList\Controller\Index;

use Magenest\MapList\Controller\DefaultController;

class Index extends DefaultController
{
    public function execute()
    {
        $this->_view->loadLayout();
        if ($block = $this->_view->getLayout()->getBlock('maplist_map_listmap')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

            $this->_view->getPage()->getConfig()->getTitle()->set(__('Store Locator'));
        $this->_view->renderLayout();
    }
}
