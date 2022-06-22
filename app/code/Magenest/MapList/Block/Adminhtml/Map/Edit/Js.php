<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/26/16
 * Time: 09:05
 */

namespace Magenest\MapList\Block\Adminhtml\Map\Edit;

use Magento\Backend\Block\Template\Context;

class Js extends \Magento\Backend\Block\Template
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        Context $context,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    public function getSelectedLocation()
    {
        $data = $this->_coreRegistry->registry('maplist_location_data');
        $locationId = array();

        if (!$data) {
            return $locationId;
        }

        foreach ($data as $value) {
            $locationId[] = $value['location_id'];
        }

        return $locationId;
    }
}
