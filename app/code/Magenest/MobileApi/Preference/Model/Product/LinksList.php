<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Preference\Model\Product;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Bundle\Pricing\Price\BundleOptionPrice;
use Magento\Bundle\Pricing\Price\BundleOptionRegularPrice;
use Magento\Bundle\Api\Data\LinkInterfaceFactory;
use Magento\Bundle\Model\Product\Type;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Product\Gallery\ReadHandler as GalleryReadHandler;

/**
 * Class LinksList
 * @package Magenest\MobileApi\Preference\Model\Product
 */
class LinksList extends \Magento\Bundle\Model\Product\LinksList
{
    /**
     * @var CatalogImageHelper
     */
    protected $_imageHelper;

    /**
     * @var Emulation
     */
    protected $_appEmulation;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var GalleryReadHandler
     */
    protected $_galleryReadHandler;

    /**
     * Constructor.
     *
     * @param LinkInterfaceFactory $linkFactory
     * @param Type $type
     * @param DataObjectHelper $dataObjectHelper
     * @param CatalogImageHelper $imageHelper
     * @param Emulation $appEmulation
     * @param StoreManagerInterface $storeManager
     * @param GalleryReadHandler $galleryReadHandler
     */
    public function __construct(
        LinkInterfaceFactory $linkFactory,
        Type $type,
        DataObjectHelper $dataObjectHelper,
        CatalogImageHelper $imageHelper,
        Emulation $appEmulation,
        StoreManagerInterface $storeManager,
        GalleryReadHandler $galleryReadHandler
    )
    {
        $this->_imageHelper = $imageHelper;
        $this->_storeManager = $storeManager;
        $this->_appEmulation = $appEmulation;
        $this->_galleryReadHandler = $galleryReadHandler;
        parent::__construct($linkFactory, $type, $dataObjectHelper);
    }

    /**
     * @inheritdoc
     */
    public function getItems(ProductInterface $product, $optionId)
    {
        $this->_appEmulation->startEnvironmentEmulation($this->_storeManager->getStore()->getId(), Area::AREA_FRONTEND, true);
        /** @var \Magento\Catalog\Model\Product $product */
        $productLinks = [];
        /** @var \Magento\Catalog\Model\Product $selection */
        foreach ($this->type->getSelectionsCollection([$optionId], $product) as $selection) {
            $this->_galleryReadHandler->execute($selection);
            $optionPriceAmount = $product->getPriceInfo()->getPrice(BundleOptionPrice::PRICE_CODE)->getOptionSelectionAmount($selection);

            //$finalPrice = $optionPriceAmount->getValue();
            $finalPrice = $selection->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue();
            $basePrice = $optionPriceAmount->getBaseAmount();
            $oldPrice = $product->getPriceInfo()->getPrice(BundleOptionRegularPrice::PRICE_CODE)->getOptionSelectionAmount($selection)->getValue();

            $productLinks[] = [
                'selection_id' => $selection->getSelectionId(),
                'qty' => $selection->getSelectionQty(),
                'canApplyMsrp' => false,
                'can_change_quantity' => $selection->getSelectionCanChangeQty(),
                'optionId' => $selection->getId(),
                'priceType' => $selection->getSelectionPriceType(),
                'name' => $selection->getName(),
                'image' => $selection->getMediaGalleryImages()->getFirstItem()->getUrl(),
                'prices' => [
                    'oldPrice' => [
                        'amount' => $oldPrice,
                    ],
                    'basePrice' => [
                        'amount' => $basePrice,
                    ],
                    'finalPrice' => [
                        'amount' => $finalPrice,
                    ],
                ]
            ];
        }

        $this->_appEmulation->stopEnvironmentEmulation();

        return $productLinks;
    }
}
