<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/24/16
 * Time: 14:36
 */

namespace Magenest\MapList\Block\Adminhtml\Holiday\Edit\Tab\AddStore;

use Magenest\MapList\Model\HolidayLocation;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Helper\Data as BackendHelper;

class Grid extends GridExtended
{
    protected $_defaultLimit = 20;
    protected $_holidayLocationFactory;
    protected $_coreRegistry = null;
    protected $_locationFactory;

    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        HolidayLocation $holidayLocationFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        array $data = array()
    ) {
        $this->_locationFactory = $locationFactory;
        $this->_holidayLocationFactory = $holidayLocationFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('maplistLocationGrid');
        $this->setDefaultSort('location_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    public function getCategory()
    {
        $holiday = $this->_coreRegistry->registry('holiday');

        return $holiday;
    }

    protected function _addColumnFilterToCollection($column)
    {
        $this->setDefaultFilter(array('category'=>20));
        // Set custom filter for in category flag
        if ($column->getId() == 'in_holiday') {
            $locationIds = $this->_getSelectedStores();
            if (empty($locationIds)) {
                $locationIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('location_id', array('in' => $locationIds));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('location_id', array('nin' => $locationIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = $this->_locationFactory->create()->getCollection()
            ->addFieldToFilter(
                'is_active',
                \Magenest\MapList\Model\Status::STATUS_ENABLED
            );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }


    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareFilterButtons()
    {
        parent::_prepareFilterButtons();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_holiday',
            array(
                'type'             => 'checkbox',
                'name'             => 'in_holiday',
                'values'           => $this->_getSelectedStores(),
                'index'            => 'location_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction',
                'filter' => false,
            )
        );
        $this->addColumn(
            'location_id',
            array(
                'header'           => __('ID'),
                'sortable'         => true,
                'index'            => 'location_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            )
        );
        $this->addColumn('title', array('header' => __('Title'), 'index' => 'title'));
        $this->addColumn('address', array('header' => __('Address'), 'index' => 'address'));
        $this->addColumn('email', array('header' => __('Email'), 'index' => 'email'));

    }


    private function _getSelectedStores()
    {
        $data = $this->_coreRegistry->registry('maplist_holiday_selected_location');
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
