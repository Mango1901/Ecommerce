<?php
namespace Magenest\Core\Plugin;

use Magento\Catalog\Block\Product\ProductList\Toolbar;
use Magento\Framework\Data\Collection;

class AddSortByProductId
{

    /**
     * @param Toolbar $subject
     * @param $result
     * @param Collection $collection
     * @return mixed
     */
    public function afterSetCollection(Toolbar $subject, $result, $collection)
    {
        $collection->setOrder(
            'product_id',
            'asc'
        );
        return $result;
    }
}
