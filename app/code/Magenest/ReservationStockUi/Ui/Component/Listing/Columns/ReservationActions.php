<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ReservationStockUi extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ReservationStockUi
 */

namespace Magenest\ReservationStockUi\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\InventoryReservationsApi\Model\ReservationInterface;
use Magenest\ReservationStockUi\Api\Data\SalesEventMetadataInterface;

class ReservationActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    protected $helper;

    protected $getViewUrlByType;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magenest\ReservationStockUi\Helper\Helper $helper
     * @param \Magenest\ReservationStockUi\Model\ResourceModel\GetViewUrlByType $viewUrlByType
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magenest\ReservationStockUi\Helper\Helper $helper,
        \Magenest\ReservationStockUi\Model\ResourceModel\GetViewUrlByType $viewUrlByType,
        array $components = [],
        array $data = []
    ) {
        $this->helper = $helper;
        $this->getViewUrlByType = $viewUrlByType;
        $this->urlBuilder = $urlBuilder;
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
                $viewUrl = $this->getViewUrl($item[ReservationInterface::METADATA]);
                $item[$this->getData('name')]['edit'] = [
                    'href' => $viewUrl,
                    'label' => __('View'),
                    'hidden' => !(bool)$viewUrl,
                ];
            }
        }

        return $dataSource;
    }

    protected function getViewUrl($metadata)
    {
        $metadata = $this->helper->unserialize($metadata);
        if (!is_array($metadata) || empty($metadata) || !isset($metadata[SalesEventMetadataInterface::EVENT_TYPE])) {
            return false;
        }

        return $this->getViewUrlByType->execute($metadata[SalesEventMetadataInterface::EVENT_TYPE], $metadata);
    }
}
