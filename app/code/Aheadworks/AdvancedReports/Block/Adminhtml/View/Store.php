<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Block\Adminhtml\View;

use Magento\Backend\Block\Template\Context;
use Aheadworks\AdvancedReports\Model\Filter\Store as StoreFilter;

/**
 * Class Store
 *
 * @package Aheadworks\AdvancedReports\Block\Adminhtml\View
 */
class Store extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_AdvancedReports::view/store.phtml';

    /**
     * @var StoreFilter
     */
    private $storeFilter;

    /**
     * Store constructor.
     * @param Context $context
     * @param StoreFilter $storeFilter
     * @param array $data
     */
    public function __construct(
        Context $context,
        StoreFilter $storeFilter,
        $data = []
    ) {
        parent::__construct($context, $data);
        $this->storeFilter = $storeFilter;
    }

    /**
     * Retrieve store items
     *
     * @return []
     */
    public function getItems()
    {
        return $this->storeFilter->getItems();
    }

    /**
     * Retrieve current item key
     *
     * @return string
     */
    public function getCurrentItemKey()
    {
        return $this->storeFilter->getCurrentItemKey();
    }

    /**
     * Retrieve store Ids
     *
     * @return \int[]|null
     */
    public function getStoreIds()
    {
        return $this->storeFilter->getStoreIds();
    }

    /**
     * Retrieve current item title
     *
     * @return string
     */
    public function getCurrentItemTitle()
    {
        $items = $this->getItems();
        $key = $this->getCurrentItemKey();
        if (!array_key_exists($key, $items)) {
            return '';
        }
        return $items[$key]['title'];
    }
}
