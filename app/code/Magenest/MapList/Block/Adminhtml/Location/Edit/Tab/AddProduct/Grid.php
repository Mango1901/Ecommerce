<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/24/16
 * Time: 14:36
 */

namespace Magenest\MapList\Block\Adminhtml\Location\Edit\Tab\AddProduct;

use Magenest\MapList\Model\StoreProduct;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended as GridExtended;
use Magento\Backend\Helper\Data as BackendHelper;

class Grid extends GridExtended
{
    protected $_defaultLimit = 20;
    protected $_storeProductFactory;
    protected $_coreRegistry = null;
    protected $_productFactory;

    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        StoreProduct $storeProductFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;
        $this->_storeProductFactory = $storeProductFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('maplistProductGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('asc');
        $this->setUseAjax(true);
    }

    public function getCategory()
    {
        $location = $this->_coreRegistry->registry('location');

        return $location;
    }

    protected function _addColumnFilterToCollection($column)
    {
        $this->setDefaultFilter(array('category'=>20));
        // Set custom filter for in category flag
        if ($column->getId() == 'in_location') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }

            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in' => $productIds));
            } elseif (!empty($productIds)) {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin' => $productIds));
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    protected function _prepareCollection()
    {
        $collection = $this->_productFactory->create()->getCollection()->addAttributeToSelect(
            'name'
        )->addAttributeToSelect(
            'sku'
        )->addAttributeToSelect(
            'price'
        )->joinField(
            'position',
            'catalog_category_product',
            'position',
            'product_id=entity_id',
            'category_id=' . (int)$this->getRequest()->getParam('id', 0),
            'left'
        );
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId > 0) {
            $collection->addStoreFilter($storeId);
        }

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
        $this
            ->addColumn(
                'in_location',
                array(
                    'type' => 'checkbox',
                    'name' => 'in_map',
                    'values' => $this->_getSelectedProducts(),
                    'index' => 'entity_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction',
                    'filter' => false,
                )
            )
            ->addColumn(
                'entity_id',
                array(
                    'header' => __('ID'),
                    'index' => 'entity_id',
                )
            )
            ->addColumn(
                'product_name',
                array(
                    'header' => __('Name'),
                    'index' => 'name',
                )
            )
            ->addColumn(
                'product_sku',
                array(
                    'header' => __('Sku'),
                    'index' => 'sku',
                )
            )
            ->addColumn(
                'product_price',
                array(
                    'header' => __('Price'),
                    'index' => 'price',
                )
            );

        // todo: render status bar on grid

        return parent::_prepareColumns();
    }


    private function _getSelectedProducts()
    {
        $data = $this->_coreRegistry->registry('maplist_location_selected_product');
        $productId = array();
        if (!$data) {
            return $productId;
        }

        foreach ($data as $value) {
            $productId[] = $value['product_id'];
        }

        return $productId;
    }
}
