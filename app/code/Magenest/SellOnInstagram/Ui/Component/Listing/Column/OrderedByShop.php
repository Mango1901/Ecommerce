<?php
namespace Magenest\SellOnInstagram\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class OrderedByShop extends Column {

    public function prepareDataSource(array $dataSource) {

        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['ordered_by_shop'] = $item['ordered_by_shop'] == 1 ? "Yes" : "No";
            }
        }
        return parent::prepareDataSource($dataSource);
    }
}
