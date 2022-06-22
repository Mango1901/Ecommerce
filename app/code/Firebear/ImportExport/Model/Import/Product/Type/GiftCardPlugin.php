<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Import\Product\Type;

use Exception;
use Firebear\ImportExport\Model\Import\Product;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogImportExport\Model\Import\Product\StoreResolver;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\GiftCardImportExport\Model\Import\Product\Type\GiftCard;

/**
 * Class GiftCardPlugin
 *
 * @package Firebear\ImportExport\Model\Import\Product\Type
 */
class GiftCardPlugin
{
    const GIFTCARD_PREFIX = 'giftcard_';

    const IS_REDEEMABLE_COLUMN = 'is_redeemable';

    const LIFETIME_COLUMN = 'lifetime';

    const ALLOW_MESSAGE_COLUMN = 'allow_message';

    const EMAIL_TEMPLATE_COLUMN = 'email_template';

    /**
     * @var Product
     */
    protected $entityModel;

    /**
     * @var StoreResolver
     */
    protected $storeResolver;

    /**
     * Product metadata pool
     *
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * Product entity link field
     *
     * @var string
     */
    protected $productEntityLinkField;

    /**
     * Giftcard amounts attribute ID
     *
     * @var int
     */
    protected $giftcardAmountAttributeId;

    /**
     * Cache for amounts
     *
     * @var array
     */
    protected $amountsCache = [];

    /**
     * Giftcard attributes with use config settings attribute
     *
     * @var array
     */
    protected $giftcardMessagingSettingsFields = [
        self::IS_REDEEMABLE_COLUMN,
        self::LIFETIME_COLUMN,
        self::EMAIL_TEMPLATE_COLUMN,
        self::ALLOW_MESSAGE_COLUMN,
    ];

    /**
     * GiftCardPlugin constructor.
     *
     * @param Product $entityModel
     * @param StoreResolver $storeResolver
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resource
     */
    public function __construct(
        Product $entityModel,
        StoreResolver $storeResolver,
        MetadataPool $metadataPool,
        ResourceConnection $resource
    ) {
        $this->entityModel = $entityModel;
        $this->storeResolver = $storeResolver;
        $this->metadataPool = $metadataPool;
        $this->resource = $resource;
    }

    /**
     * Prepare attributes with default value for save
     *
     * @param GiftCard $subject
     * @param array $rowData
     * @param bool $withDefaultValue
     * @return array
     */
    public function beforePrepareAttributesWithDefaultValueForSave(
        GiftCard $subject,
        array $rowData,
        $withDefaultValue = true
    ) {
        foreach ($this->giftcardMessagingSettingsFields as $columnName) {
            $fieldName = self::GIFTCARD_PREFIX . $columnName;
            if (!empty($rowData[$columnName])) {
                $rowData[$fieldName] = $rowData[$columnName];
            }
        }
        return [$rowData, $withDefaultValue];
    }

    /**
     * Save Giftcard data
     *
     * @param GiftCard $subject
     * @param callable $proceed
     * @return GiftCard
     * @throws Exception
     */
    public function aroundSaveData(GiftCard $subject, callable $proceed)
    {
        while ($bunch = $this->entityModel->getNextBunch()) {
            $newSku = $this->entityModel->getNewSku();
            foreach ($bunch as $rowNum => $rowData) {
                if (isset($newSku[strtolower($rowData[Product::COL_SKU])])) {
                    $productData = $newSku[strtolower($rowData[Product::COL_SKU])];
                    if (!$this->entityModel->isRowAllowedToImport($rowData, $rowNum)
                        || 'giftcard' != $productData['type_id']) {
                        continue;
                    }
                    $this->parseAmounts($rowData, $productData[$this->getProductEntityLinkField()], $subject);
                    if (!empty($this->amountsCache)) {
                        $this->insertAmounts()->clearAmountsCache();
                    }
                }
            }
        }

        return $subject;
    }

    /**
     * Parse giftcard amounts values
     *
     * @param array $rowData
     * @param int $entityId
     * @param GiftCard $subject
     * @return $this
     * @throws Exception
     */
    protected function parseAmounts($rowData, $entityId, $subject)
    {
        if (isset($rowData[GiftCard::GIFTCARD_AMOUNT_COLUMN])) {
            $amounts = explode(
                $this->entityModel->getMultipleValueSeparator(),
                trim($rowData[GiftCard::GIFTCARD_AMOUNT_COLUMN])
            );
            $amountData['website_id'] = (isset($rowData['website_code']))
                ? $this->storeResolver->getWebsiteCodeToId($rowData['website_code'])
                : GiftCard::DEFAULT_WEBSITE_ID;
            $amountData['attribute_id'] = $this->getGiftcardAmountsAttributeId($subject);
            $amountData[$this->getProductEntityLinkField()] = $entityId;
            foreach ($amounts as $amount) {
                if (!$this->isAmountExists($amount, $amountData)) {
                    $amountData['value'] = $amount;
                    $this->amountsCache[] = $amountData;
                }
            }
        }
        return $this;
    }

    /**
     * Check if amount value exists
     *
     * @param float $amount
     * @param array $amountData
     * @return bool
     * @throws Exception
     */
    protected function isAmountExists($amount, $amountData)
    {
        foreach ($this->amountsCache as $amounts) {
            if ($amounts['website_id'] == $amountData['website_id'] &&
                $amounts['attribute_id'] == $amountData['attribute_id'] &&
                $amounts['value'] == $amount &&
                $amounts['row_id'] == $amountData[$this->getProductEntityLinkField()]
            ) {
                return true;
            }
        }
        $amountTable = $this->resource->getTableName(GiftCard::GIFTCARD_AMOUNT_TABLE);
        /** @var Select $select */
        $select = $this->entityModel->getConnection()->select();
        $select->from($amountTable, 'value_id')
            ->where('website_id = ?', $amountData['website_id'])
            ->where('attribute_id = ?', $amountData['attribute_id'])
            ->where('row_id = ?', $amountData[$this->getProductEntityLinkField()])
            ->where('value = ?', $amount);

        return (bool)$this->entityModel->getConnection()->fetchOne($select);
    }

    /**
     * Insert amounts from bunch
     *
     * @return $this
     */
    protected function insertAmounts()
    {
        $amountTable = $this->resource->getTableName(GiftCard::GIFTCARD_AMOUNT_TABLE);
        $this->entityModel->getConnection()->insertOnDuplicate($amountTable, $this->amountsCache);

        return $this;
    }

    /**
     * Clear cached amount values
     *
     * @return $this
     */
    protected function clearAmountsCache()
    {
        $this->amountsCache = [];

        return $this;
    }

    /**
     * Get product entity link field
     *
     * @return string
     * @throws Exception
     */
    protected function getProductEntityLinkField()
    {
        if (!$this->productEntityLinkField) {
            $this->productEntityLinkField = $this->metadataPool
                ->getMetadata(ProductInterface::class)
                ->getLinkField();
        }
        return $this->productEntityLinkField;
    }

    /**
     * Check if giftcard amounts attribute id exists
     *
     * @param GiftCard $subject
     * @return int
     */
    protected function getGiftcardAmountsAttributeId(GiftCard $subject)
    {
        if (!$this->giftcardAmountAttributeId) {
            $this->giftcardAmountAttributeId
                = isset($subject->retrieveAttributeFromCache(GiftCard::AMOUNT_ATTRIBUTE_NAME)['id'])
                ? $subject->retrieveAttributeFromCache(GiftCard::AMOUNT_ATTRIBUTE_NAME)['id']
                : null;
        }

        return $this->giftcardAmountAttributeId;
    }
}
