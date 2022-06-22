<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\Manufacturer;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;

/**
 * Class Manufacturer
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\Manufacturer
 */
class Manufacturer extends \Magento\Ui\Component\Listing\Columns\Column
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
                if ($item['manufacturer'] == 'Not Set') {
                    $item['row_url'] = '';
                } else {
                    $params = ['manufacturer' => base64_encode($item['manufacturer'])];
                    $item['row_url'] = $this->urlModel->getUrl(
                        'manufacturer',
                        'productperformance',
                        $params
                    );
                }
                $item['row_label'] = $item['manufacturer'];
            }
        }
        return $dataSource;
    }
}
