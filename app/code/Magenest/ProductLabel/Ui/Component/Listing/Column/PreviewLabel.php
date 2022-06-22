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
use Magento\Framework\View\Element\UiComponentFactory;
use Magenest\ProductLabel\Api\LabelRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class PreviewLabel extends Column
{

    /**
     * @var LabelRepositoryInterface
     */
    private $labelRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    private $productCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    private $productVisibility;

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var null|\Magento\Catalog\Model\Product
     */
    private $product = null;

    /**
     * PreviewLabel constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param LabelRepositoryInterface $labelRepository
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\Product\Visibility $visibility
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        array $components = [],
        array $data = [])
    {
        $this->labelRepository = $labelRepository;
        $this->productCollection = $collectionFactory;
        $this->productVisibility = $visibility;
        $this->layoutFactory = $layoutFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['label_id'])) {
                    $config = $this->getData();
                    $label = $this->labelRepository->get($item['label_id']);
                    $data = $label->getData($config['name']);
                    $data['display'] = 1;
                    $label->setData($config['name'], $data);
                    $item[$this->getData('name')] = $this->generateLabel($label, $config['name']);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Generate label html
     *
     * @param $label
     * @param $page
     * @return string
     */
    private function generateLabel($label, $page)
    {
        $data = [
            'page' => $page,
            'is_active' => true,
            'label_object' => $label
        ];
        $block = $this->layoutFactory->create()->createBlock(\Magenest\ProductLabel\Block\Label::class)
            ->setTemplate('Magenest_ProductLabel::label_detail.phtml')
            ->setData($data);
        $html = $block->toHtml();

        return $html;
    }
}
