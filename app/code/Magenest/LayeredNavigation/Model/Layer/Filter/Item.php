<?php

namespace Magenest\LayeredNavigation\Model\Layer\Filter;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager;

/**
 * Class Item
 * @package Magenest\LayeredNavigation\Model\Layer\Filter
 */
class Item extends \Magento\Catalog\Model\Layer\Filter\Item
{
    /**
     * Item constructor.
     * @param UrlInterface $url
     * @param Pager $htmlPagerBlock
     * @param array $data
     */
    public function __construct(
        UrlInterface $url,
        Pager $htmlPagerBlock,
        array $data = []
    )
    {
        $this->_url            = $url;
        $this->_htmlPagerBlock = $htmlPagerBlock;
        parent::__construct($url, $htmlPagerBlock, $data);
    }

    public function getUrl()
    {
        $filter         = $this->getFilter();
        $filterUrlValue = $this->getValue();

        if ($filter->getAppliedFilter()) {
            $filterUrlValue = $filter->getAppliedFilter() . ',' . $this->getValue();
        }

        $query = [
            $filter->getRequestVar() => $filterUrlValue,
            // exclude current page from urls
            $this->_htmlPagerBlock->getPageVarName() => null,
            '_' => null
        ];

        $itemUrl = $this->_url->getUrl(
            '*/*/*', [
                '_current' => true,
                '_use_rewrite' => true,
                '_escape' => true,
                '_query' => $query
            ]
        );
        return urldecode($itemUrl);
    }

    public function getRemoveUrl()
    {
        $filterUrlValue = $this->getValue();
        $query          = [];
        if ($this->getFilter()->getAppliedFilter()) {
            $activeFilters = explode(',', $this->getFilter()->getAppliedFilter());
            foreach ($activeFilters as $activeFilter) {
                if ($filterUrlValue != $activeFilter) {
                    $query[] = $activeFilter;
                }
            }
        }
        $removeValue = null;
        if (count($query) > 0) {
            $removeValue = implode(',', $query);
        }
        $query = [
            $this->getFilter()->getRequestVar() => $removeValue,
            '_' => null
        ];

        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = $query;
        $params['_escape']      = true;
        $removeUrl              = $this->_url->getUrl('*/*/*', $params);
        return urldecode($removeUrl);
    }


    public function getValueString()
    {
        $value = $this->getFilter()->getAppliedFilter();

        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }
}
