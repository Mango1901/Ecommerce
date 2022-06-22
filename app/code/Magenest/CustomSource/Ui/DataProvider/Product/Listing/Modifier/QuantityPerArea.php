<?php

namespace Magenest\CustomSource\Ui\DataProvider\Product\Listing\Modifier;

use Magenest\CustomSource\Helper\Data;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\ObjectManager;
use Magento\InventoryCatalogApi\Model\IsSingleSourceModeInterface;
use Magento\InventoryConfigurationApi\Model\GetAllowedProductTypesForSourceItemManagementInterface;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Listing\Columns\Column;
use Magenest\CustomSource\Model\Source\Area\Options;
use Psr\Log\LoggerInterface;

/**
 * Quantity Per Area modifier on CatalogInventory Product Grid
 */
class QuantityPerArea extends AbstractModifier
{
    /**
     * @var IsSingleSourceModeInterface
     */
    private $isSingleSourceMode;

    /**
     * @var GetAllowedProductTypesForSourceItemManagementInterface
     */
    private $getAllowedProductTypesForSourceItemManagement;

    /**
     * @var Options
     */
    private $options;

    /**
     * @var Data
     */
    private $dataHelper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param IsSingleSourceModeInterface $isSingleSourceMode
     * @param Options $options
     * @param Data $dataHelper
     * @param LoggerInterface $logger
     * @param GetAllowedProductTypesForSourceItemManagementInterface|null $getAllowedProductTypesForSourceItemManagement
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        IsSingleSourceModeInterface $isSingleSourceMode,
        Options $options,
        Data $dataHelper,
        LoggerInterface $logger,
        GetAllowedProductTypesForSourceItemManagementInterface $getAllowedProductTypesForSourceItemManagement = null
    ) {
        $objectManager = ObjectManager::getInstance();
        $this->isSingleSourceMode = $isSingleSourceMode;
        $this->options = $options;
        $this->dataHelper = $dataHelper;
        $this->getAllowedProductTypesForSourceItemManagement = $getAllowedProductTypesForSourceItemManagement ?:
            $objectManager->get(GetAllowedProductTypesForSourceItemManagementInterface::class);
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        if (0 === $data['totalRecords'] || true === $this->isSingleSourceMode->execute()) {
            return $data;
        }

        $data['items'] = $this->getAreaItemsData($data['items']);

        return $data;
    }

    /**
     * Add qty per area to the items.
     *
     * @param array $dataItems
     * @return array
     */
    private function getAreaItemsData(array $dataItems): array
    {
        try {
            $itemsBySkus = [];
            $allowedProductTypes = $this->getAllowedProductTypesForSourceItemManagement->execute();
            $skus = [];

            foreach ($dataItems as $key => $item) {
                if (in_array($item['type_id'], $allowedProductTypes)) {
                    $itemsBySkus[$item['sku']] = $key;
                    $skus[] = $item['sku'];
                    continue;
                }
                $dataItems[$key]['quantity_per_area'] = [];
            }

            unset($item);
            $areasOptionData = $this->options->toOptionArray();

            foreach ($areasOptionData as $areaOption) {
                $inventorySourceItems = $this->dataHelper->getDataIsSalableOfProduct($areaOption['value'], $skus);

                foreach ($inventorySourceItems as $sourceItem) {
                    if (isset($itemsBySkus[$sourceItem['sku']])) {
                        $dataItems[$itemsBySkus[$sourceItem['sku']]]['quantity_per_area'][] = [
                            'area_name' => $areaOption['label'],
                            'area_code' => $areaOption['value'],
                            'qty' => round($sourceItem['qty']),
                        ];
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $dataItems;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if (true === $this->isSingleSourceMode->execute()) {
            return $meta;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'product_columns' => [
                    'children' => [
                        'quantity_per_area' => $this->getQuantityPerAreaMeta(),
                        'qty' => [
                            'arguments' => null,
                        ],
                    ],
                ],
            ]
        );
        return $meta;
    }

    /**
     * Qty per area metadata for rendering.
     *
     * @return array
     */
    private function getQuantityPerAreaMeta(): array
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'sortOrder' => 76,
                        'filter' => false,
                        'sortable' => false,
                        'label' => __('Quantity per Area'),
                        'dataType' => Text::NAME,
                        'componentType' => Column::NAME,
                        'resizeEnabled' => false,
                        'resizeDefaultWidth' => 200,
                        'component' => 'Magenest_CustomSource/js/product/grid/cell/quantity-per-area',
                    ]
                ],
            ],
        ];
    }
}
