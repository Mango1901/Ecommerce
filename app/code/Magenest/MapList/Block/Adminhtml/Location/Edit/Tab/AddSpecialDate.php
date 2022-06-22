<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MapList\Block\Adminhtml\Location\Edit\Tab;

use Magento\Backend\Block\Widget;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class AddSpecialDate extends \Magento\Backend\Block\Widget implements TabInterface
{
    protected $_template = "map/tab/special_date.phtml";
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
    public function getSpecialDateDate()
    {
        $model = $this->_coreRegistry->registry('maplist_location_edit');
        $data = $model->getData();
        $this->jsLayout['components']['mapspecialdate']['component'] = "Magenest_MapList/js/addspecialdate";
        $this->jsLayout['components']['mapspecialdate']['template'] = "Magenest_MapList/options";
        if (isset($data['special_date'])) {
            $this->jsLayout['components']['mapspecialdate']['config']['specialDate'] = $data['special_date'];
        } else {
            $this->jsLayout['components']['mapspecialdate']['config']['specialDate'] = "[{\"special_date\":\"\",\"id\":\"0\",\"description\":\"\"}]";
        }

        return json_encode($this->jsLayout);
    }
    public function getTabLabel()
    {
        return __("Special Date");
    }
    public function getTabTitle()
    {
        return __("Special Date");
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }
}
