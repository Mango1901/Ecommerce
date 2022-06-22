<?php

namespace Magenest\LayeredNavigation\Model\Layer\Filter;

use Magenest\LayeredNavigation\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection;
use Magento\Framework\App\RequestInterface;

class Attribute extends \Magento\CatalogSearch\Model\Layer\Filter\Attribute
{
    const FILTERABLE_NO_RESULTS = 2;

    /**
     * @var \Magento\Framework\Filter\StripTags
     */
    private $tagFilter;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * Attribute constructor.
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param RequestInterface $request
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory      $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface           $storeManager,
        \Magento\Catalog\Model\Layer                         $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Filter\StripTags                  $tagFilter,
        RequestInterface                                     $request,
        array                                                $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $tagFilter, $data);
        $this->tagFilter = $tagFilter;
        $this->request   = $request;
    }

    /**
     * Build option data
     *
     * @param array $option
     * @param boolean $isAttributeFilterable
     * @param array $optionsFacetedData
     *
     * @return void
     */
    private function buildOptionData($option, $isAttributeFilterable, $optionsFacetedData)
    {
        $value = $this->getOptionValue($option);
        if ($value === false) {
            return;
        }
        $count = $this->getOptionCount($value, $optionsFacetedData);
        if ($isAttributeFilterable && $count === 0) {
            return;
        }

        $this->itemDataBuilder->addItemData(
            $this->tagFilter->filter($option['label']),
            $value,
            $count
        );
    }

    /**
     * Retrieve option value if it exists
     *
     * @param array $option
     *
     * @return bool|string
     */
    private function getOptionValue($option)
    {
        if (empty($option['value']) && !is_numeric($option['value'])) {
            return false;
        }
        return $option['value'];
    }

    /**
     * Retrieve count of the options
     *
     * @param int|string $value
     * @param array $optionsFacetedData
     *
     * @return int
     */
    private function getOptionCount($value, $optionsFacetedData)
    {
        return isset($optionsFacetedData[$value]['count'])
            ? (int)$optionsFacetedData[$value]['count']
            : 0;
    }

    /**
     * Convert attribute value according to its backend type.
     *
     * @param ProductAttributeInterface $attribute
     * @param mixed $value
     *
     * @return int|string
     */
    private function convertAttributeValue(ProductAttributeInterface $attribute, $value)
    {
        if ($attribute->getBackendType() === 'int') {
            return (int)$value;
        }

        return $value;
    }

    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        /** @var Collection $productCollection */
        $productCollection  = $this->getLayer()->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

        $isShowEmptyResults    = $this->getAttributeIsFilterable($attribute) === self::FILTERABLE_NO_RESULTS;
        $isAttributeFilterable =
            $this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;

        if (count($optionsFacetedData) === 0 && !$isShowEmptyResults) {
            return $this->itemDataBuilder->build();
        }

        $options = $attribute->getFrontend()->getSelectOptions();
        foreach ($options as $option) {
            $this->buildOptionData($option, $isAttributeFilterable, $optionsFacetedData);
        }

        return $this->itemDataBuilder->build();
    }

    public function apply(RequestInterface $request)
    {
        parent::apply($request);
        if (is_array($this->_items) && !count($this->_items)) {
            $this->_items = null;
        }
        return $this;
    }

    protected function _initItems()
    {
        $data  = $this->_getItemsData();
        $items = [];
        foreach ($data as $itemData) {
            $item = $this->_createItem($itemData['label'], $itemData['value'], $itemData['count']);
            $this->isActiveItem($item);
            $items[] = $item;
        }
        $this->_items = $items;
        return $this;
    }

    protected function isActiveItem($item)
    {
        $value = $this->request->getParam($this->getRequestVar());
        $item->setActive($item->getValue() == $value);
    }
}
