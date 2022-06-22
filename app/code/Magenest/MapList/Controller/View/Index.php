<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/16/16
 * Time: 21:53
 */

namespace Magenest\MapList\Controller\View;

use Magenest\MapList\Controller\DefaultController;

class Index extends DefaultController
{
    public function execute()
    {
        $locationId = $this->getRequest()->getParam('id');
        $locationModel = $this->_locationFactory->create();
        $location = $locationModel->load($locationId);
        if (!$location->getData()) {  //if $location id not found
            $this->_forward('noroute');
        }

        //if current location is not active ~~> forward not found
        if ($location->getData('is_active') == 0) {
            $this->_forward('noroute');
        }

        $map = $location->getData('title');
        $page = $this->resultPageFactory->create();
        $page->getConfig()->getTitle()->set(__($map));
        $this->_coreRegistry->register('maplist_location_model', $location);

        return $this->resultPageFactory->create();
    }
}
