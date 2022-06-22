<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

class LabelStatus extends Column
{
    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if(isset($dataSource['data']['items'])){
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['status'])) {
                    switch ($item['status']) {
                        case 0:
                            $html = '<span class="grid-severity-critical"><span>' . __('Inactive') . '</span>' . '</span>';
                            break;
                        case 1:
                            $html = '<span class="grid-severity-notice"><span>' . __('Active') . '</span>' . '</span>';
                            break;
                    }
                }
                $item[$this->getData('name')] = html_entity_decode($html);
            }
        }

        return $dataSource;
    }
}
