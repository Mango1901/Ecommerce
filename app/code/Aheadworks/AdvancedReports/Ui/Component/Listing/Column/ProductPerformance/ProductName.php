<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\ProductPerformance;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;

/**
 * Class ProductName
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\ProductPerformance
 */
class ProductName extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlModel $urlModel
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlModel $urlModel,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlModel = $urlModel;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $params = array_merge(
                    $this->context->getDataProvider()->getAllowedRequestParams(),
                    [
                        'product_id' => $item['product_id'],
                        'product_name' => base64_encode($item['product_name'])
                    ]
                );
                if (isset($item['parent_id']) && $item['parent_id']) {
                    $params['parent_id'] = $item['parent_id'];
                }

                $item['row_url'] = $this->urlModel->getUrl(
                    'productperformance',
                    'productperformance_variantperformance',
                    $params
                );
                $item['row_label'] = $item['product_name'];
            }
        }
        return $dataSource;
    }
}
