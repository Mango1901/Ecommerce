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

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_url;

    /**
     * Actions constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $url
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $url,
        array $components = [],
        array $data = []
    )
    {
        $this->_url = $url;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = [
                    'label' => 'Edit',
                    'href' => $this->_url->getUrl('catalog/label/edit', ['label_id' => $item['label_id']]),
                    'hidden' => false
                ];
                $item[$this->getData('name')]['delete'] = [
                    'label' => 'Delete',
                    'href' => $this->_url->getUrl('catalog/label/delete', ['label_id' => $item['label_id']]),
                    'hidden' => false,
                    'confirm' => [
                        'message' => __('Are you sure you want to do this?')
                    ],
                ];
            }
        }

        return $dataSource;
    }
}
