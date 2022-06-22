<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 15/09/2016
 * Time: 08:16
 */

namespace Magenest\MapList\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class BrandActions extends Column
{
    protected $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $id = $this->context->getFilterParam('brand_id');
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = array(
                    'href' => $this->urlBuilder->getUrl(
                        'maplist/brand/edit',
                        array(
                            'id' => $item['brand_id'],
                        )
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                );

                $item[$this->getData('name')]['delete'] = array(
                    'href' => $this->urlBuilder->getUrl(
                        'maplist/brand/delete',
                        array(
                            'id' => $item['brand_id'],
                        )
                    ),
                    'label' => __('Delete'),
                    'hidden' => false,
                );
            }
        }

        return $dataSource;
    }
}
