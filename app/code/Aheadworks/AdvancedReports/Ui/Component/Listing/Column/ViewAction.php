<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column;

/**
 * Class ViewAction
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column
 */
class ViewAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $viewUrlPath = $this->getData('config/viewUrlPath') ?: '#';
            $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'id';
            $urlEntityParamValue = $this->getData('config/urlEntityParamValue') ?: 'id';
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$urlEntityParamValue]) && $item[$urlEntityParamValue]) {
                    $item['row_url_' . $fieldName] = $this->context->getUrl(
                        $viewUrlPath,
                        [$urlEntityParamName => $item[$urlEntityParamValue]]
                    );
                } else {
                    $item['row_url_' . $fieldName] = '';
                }
                $item['row_label_' . $fieldName] = $item[$fieldName];
            }
        }
        return $dataSource;
    }
}
