<?php

namespace Magenest\Affiliate\Ui\Component\Listing\Column;

use Magenest\Affiliate\Helper\Data;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Custom Display Qty Of Grid
 */
class TotalQty extends Column
{
    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$fieldName])) {
                    $qty = $item[$fieldName];
                    $item[$fieldName] = Data::niceNumberFormat($qty);
                }
            }
        }

        return $dataSource;
    }
}
