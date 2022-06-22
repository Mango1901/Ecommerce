<?php

namespace Magenest\SellOnInstagram\Ui\Component\Listing\Column\Mapping;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class Actions
 * @package Magenest\SellOnInstagram\Ui\Component\Listing\Column\Mapping
 */
class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Actions constructor.
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                'instagramshop/mapping/edit', [
                                                              'id' => $item['id']
                                                          ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                'instagramshop/mapping/delete', [
                                                                'id' => $item['id']
                                                            ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Template'),
                                'message' => __('Are you sure you want to delete ?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
