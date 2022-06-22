<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/24/16
 * Time: 14:36
 */

namespace Magenest\MapList\Block\Adminhtml\Map\Edit\Tab\AddLocation;

use Magenest\MapList\Model\LocationFactory;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Helper\Data as BackendHelper;

class Grid extends GridExtended
{
    protected $_defaultLimit = 0;
    protected $_locationFactory;
    protected $_coreRegistry;

    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        LocationFactory $locationFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_locationFactory = $locationFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('maplistMapGrid');
        $this->setDefaultSort('location_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
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

    protected function _prepareCollection()
    {
        $this->setCollection($this->_locationFactory->create()->getCollection());

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_map',
            array(
                'type' => 'checkbox',
                'name' => 'in_map',
                'values' => $this->_getSelectedProducts(),
                'index' => 'location_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction',
                'filter' => false,
            )
        )
            ->addColumn(
                'location_id',
                array(
                    'header' => __('ID'),
                    'index' => 'location_id',
                )
            )
            ->addColumn(
                'location_title',
                array(
                    'header' => __('Title'),
                    'index' => 'title',
                )
            )
            ->addColumn(
                'location_short_description',
                array(
                    'header' => __('Description'),
                    'index' => 'short_description',
                )
            )
            ->addColumn(
                'location_image',
                array(
                    'header' => __('Map'),
                    'index' => 'image',
                    'renderer' => 'Magenest\MapList\Block\Adminhtml\Map\Edit\Tab\AddLocation\Renderer\Image',
                    'sortable' => false,
                    'filter' => false,
                )
            )
            ->addColumn(
                'action',
                array(
                    'header' => __('Action'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => __('Edit'),
                            'url' => array(
                                'base' => 'maplist/location/edit',
                            ),
                            'field' => 'id',
                        ),
                    ),
                    'sortable' => false,
                    'filter' => false,
                )
            );

        // todo: render status bar on grid

        return parent::_prepareColumns();
    }

    private function _getSelectedProducts()
    {
        $locationData = $this->_coreRegistry->registry('maplist_location_data');
        $locationId = array();
        if (!$locationData) {
            return $locationId;
        }

        foreach ($locationData as $locationValue) {
            $locationId[] = $locationValue['location_id'];
        }

        return $locationId;
    }
}
