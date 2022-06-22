<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/23/16
 * Time: 14:34
 */

namespace Magenest\MapList\Block\Adminhtml\Holiday\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;

class Js extends \Magento\Backend\Block\Template
{
    protected $_coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_scopeConfig = $context->getScopeConfig();
        parent::__construct($context, $data);
    }


    public function getSelectedStore()
    {
        $data = $this->_coreRegistry->registry('maplist_holiday_selected_location');
        $storeId = array();
        if (!$data) {
            return $storeId;
        }

        foreach ($data as $value) {
            $storeId[] = $value['location_id'];
        }

        return $storeId;
    }
}
