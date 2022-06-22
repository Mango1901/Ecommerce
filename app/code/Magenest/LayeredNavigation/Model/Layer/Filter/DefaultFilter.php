<?php

namespace Magenest\LayeredNavigation\Model\Layer\Filter;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;

abstract class DefaultFilter extends AbstractFilter implements FilterInterface
{
    const ONLY_WITH_RESULT = 1;

    protected $_filterItemsCount;
    /**
     * @var ItemBuilder
     */
    protected $itemBuilder;

    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        ItemBuilder $itemBuilder,
        array $data = []
    )
    {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->itemBuilder = $itemBuilder;
    }

    public function getRequestVar()
    {
        return $this->_requestVar;
    }

    public function apply(RequestInterface $request)
    {
        return $this;
    }

    public function getResetValue()
    {
        $result = null;
        return $result;
    }

    public function getCleanValue()
    {
        $result = null;
        return $result;
    }

    public function getFilterItemsCount()
    {
        if ($this->_filterItemsCount === null) {
            $this->_initFilterItemsCount();
        }
        return $this->_filterItemsCount;
    }

    protected function _initFilterItemsCount()
    {
        $this->_filterItemsCount = $this->_getDefaultItemsCount();
        return $this;
    }

    protected function _getDefaultItemsCount()
    {
        $itemsCount = [];
        return $itemsCount;
    }

    public function getItemsCount()
    {
        $items = $this->getItems();
        return count($items);
    }

    public function getItems()
    {
        if ($this->_items === null) {
            $this->_initItems();
        }
        return $this->_items;
    }

    protected function _initItems()
    {
        $itemsData = $this->_getItemsData();
        $itemList  = [];
        foreach ($itemsData as $itemData) {
            $itemList[] = $this->_createItem(
                $itemData['label'],
                $itemData['value'],
                $itemData['count'],
                $itemData['active'],
                $itemData['plus']
            );
        }

        $this->_items = $itemList;
        return $this;
    }


    protected function _createItem($itemLabel, $itemValue, $itemCount = 0, $active = false, $plus = false)
    {
        return $this->_filterItemFactory->create()
            ->setFilter($this)
            ->setLabel($itemLabel)
            ->setValue($itemValue)
            ->setCount($itemCount)
            ->setActive($active)
            ->setPlus($plus);
    }

    public function setItems(array $itemList)
    {
        $this->_items = $itemList;
        return $this;
    }

    public function setAttributeModel($attributeModel)
    {
        $code = $attributeModel->getAttributeCode();
        $this->setRequestVar($code);
        $this->setData('attribute_model', $attributeModel);
        return $this;
    }

    public function setRequestVar($varName)
    {
        $this->_requestVar = $varName;
        return $this;
    }

    public function getName()
    {
        $name = $this->getAttributeModel()->getStoreLabel();
        return $name;
    }

    public function getAttributeModel()
    {
        $attributeModel = $this->getData('attribute_model');
        if (null === $attributeModel) {
            throw new LocalizedException(
                __('The attribute model is not defined.')
            );
        }
        return $attributeModel;
    }

    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    public function getStoreId()
    {
        $id = $this->_getData('store_id');
        if (null === $id) {
            $id = $this->_storeManager->getStore()->getId();
        }
        return $id;
    }

    public function getWebsiteId()
    {
        $website = $this->_getData('website_id');
        if (null === $website) {
            $website = $this->_storeManager->getStore()->getWebsiteId();
        }
        return $website;
    }

    public function setWebsiteId($websiteId)
    {
        return $this->setData('website_id', $websiteId);
    }

    public function getClearLinkText()
    {
        return false;
    }

    public function getLayer()
    {
        $layer = $this->_getData('layer');
        if ($layer === null) {
            $layer = $this->_catalogLayer;
            $this->setData('layer', $layer);
        }
        return $layer;
    }

    protected function getOptionText($id)
    {
        $attribute = $this->getAttributeModel();
        return $attribute->getFrontend()->getOption($id);
    }

    protected function getAttributeIsFilterable($attr)
    {
        return (int)$attr->getIsFilterable();
    }
}
