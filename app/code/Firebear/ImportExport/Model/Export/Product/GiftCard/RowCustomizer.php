<?php
/**
 * @copyright: Copyright © 2018 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */
namespace Firebear\ImportExport\Model\Export\Product\GiftCard;

use Magento\CatalogImportExport\Model\Export\RowCustomizerInterface;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\GiftCard\Model\Catalog\Product\Type\Giftcard;
use Magento\GiftCard\Model\Giftcard as ModelGiftcard;

/**
 * GiftCard row customizer
 */
class RowCustomizer implements RowCustomizerInterface
{
    /**
     * Column names
     */
    const GIFTCARD_TYPE_COLUMN = 'giftcard_type';

    const GIFTCARD_AMOUNT_COLUMN = 'giftcard_amount';

    const ALLOW_OPEN_AMOUNT_COLUMN = 'giftcard_allow_open_amount';

    /**
     * @var array
     */
    protected $giftcardData = [];

    /**
     * @var string[]
     */
    private $giftcardColumns = [
        self::GIFTCARD_TYPE_COLUMN,
        self::GIFTCARD_AMOUNT_COLUMN,
        self::ALLOW_OPEN_AMOUNT_COLUMN
    ];

    /**
     * Mapping for giftcard types
     *
     * @var array
     */
    protected $typeMapping = [
        ModelGiftcard::TYPE_VIRTUAL => 'Virtual',
        ModelGiftcard::TYPE_PHYSICAL => 'Physical',
        ModelGiftcard::TYPE_COMBINED => 'Combined'
    ];

    /**
     * Prepare data for export
     *
     * @param ProductCollection $collection
     * @param int[] $productIds
     * @return void
     */
    public function prepareData($collection, $productIds)
    {
        $productCollection = clone $collection;
        $productCollection->addAttributeToFilter('entity_id', ['in' => $productIds])
            ->addAttributeToFilter('type_id', ['eq' => Giftcard::TYPE_GIFTCARD])
            ->addAttributeToSelect(['giftcard_type', 'allow_open_amount']);

        foreach ($productCollection as $product) {
            $this->giftcardData[$product->getId()] = [
                self::GIFTCARD_TYPE_COLUMN => $this->getTypeValue($product->getGiftcardType()),
                self::GIFTCARD_AMOUNT_COLUMN => $this->getAmountValue($product->getGiftcardAmounts()),
                self::ALLOW_OPEN_AMOUNT_COLUMN => $product->getAllowOpenAmount(),
            ];
        }
    }

    /**
     * Retrieve card type value by code
     *
     * @param string $type
     * @return string
     */
    protected function getTypeValue($type)
    {
        return isset($this->typeMapping[$type]) ? $this->typeMapping[$type] : ModelGiftcard::TYPE_COMBINED;
    }

    /**
     * Retrieve card type value by code
     *
     * @param array $amounts
     * @return array
     */
    public function getAmountValue($amounts)
    {
        $values = [];
        foreach ($amounts ?: [] as $amount) {
            $values[] = $amount['value'] ?? 0;
        }
        return implode(',', $values);
    }

    /**
     * Set headers columns
     *
     * @param array $columns
     * @return array
     */
    public function addHeaderColumns($columns)
    {
        return array_merge($columns, $this->giftcardColumns);
    }

    /**
     * Add data for export
     *
     * @param array $dataRow
     * @param int $productId
     * @return array
     */
    public function addData($dataRow, $productId)
    {
        if (!empty($this->giftcardData[$productId])) {
            $dataRow = array_merge($dataRow, $this->giftcardData[$productId]);
        }
        return $dataRow;
    }

    /**
     * Calculate the largest links block
     *
     * @param array $additionalRowsCount
     * @param int $productId
     * @return array
     */
    public function getAdditionalRowsCount($additionalRowsCount, $productId)
    {
        if (!empty($this->giftcardData[$productId])) {
            $additionalRowsCount = max($additionalRowsCount, count($this->giftcardData[$productId]));
        }
        return $additionalRowsCount;
    }
}
