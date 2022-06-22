<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magenest\MegaMenu\Model\Export;

use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Data\Collection;
use Magento\ImportExport\Model\Export\Factory as CollectionFactory;
use Magento\InventoryApi\Api\Data\SourceItemInterface;
use Magento\InventoryImportExport\Model\Export\Source\StockStatus;

/**
 * @api
 */
class AttributeCollectionProvider
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var AttributeFactory
     */
    private $attributeFactory;

    /**
     * @param CollectionFactory $collectionFactory
     * @param AttributeFactory $attributeFactory
     * @throws \InvalidArgumentException
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        AttributeFactory $attributeFactory
    ) {
        $this->collection = $collectionFactory->create(Collection::class);
        $this->attributeFactory = $attributeFactory;
    }

    /**
     * @return Collection
     * @throws \Exception
     */
    public function get(): Collection
    {
        if (count($this->collection) === 0) {
            /** @var \Magento\Eav\Model\Entity\Attribute $menuIdAttribute */
			$menuIdAttribute = $this->attributeFactory->create();
			$menuIdAttribute->setId('menu_id');
			$menuIdAttribute->setDefaultFrontendLabel('menu_id');
			$menuIdAttribute->setAttributeCode('menu_id');
			$menuIdAttribute->setBackendType('int');
            $this->collection->addItem($menuIdAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $menuNameAttribute */
			$menuNameAttribute = $this->attributeFactory->create();
			$menuNameAttribute->setId('menu_name');
			$menuNameAttribute->setBackendType('text');
			$menuNameAttribute->setDefaultFrontendLabel('menu_name');
			$menuNameAttribute->setAttributeCode('menu_name');
            $this->collection->addItem($menuNameAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $storeIdAttribute */
			$storeIdAttribute = $this->attributeFactory->create();
			$storeIdAttribute->setId('store_id');
			$storeIdAttribute->setDefaultFrontendLabel('store_id');
			$storeIdAttribute->setAttributeCode('store_id');
			$storeIdAttribute->setBackendType('int');
            $this->collection->addItem($storeIdAttribute);

            /** @var \Magento\Eav\Model\Entity\Attribute $menuTemplateAttribute */
			$menuTemplateAttribute = $this->attributeFactory->create();
			$menuTemplateAttribute->setId('menu_template');
			$menuTemplateAttribute->setBackendType('text');
			$menuTemplateAttribute->setDefaultFrontendLabel('menu_template');
			$menuTemplateAttribute->setAttributeCode('menu_template');
			$menuTemplateAttribute->setFrontendInput('select');
			$menuTemplateAttribute->setSourceModel(\Magenest\MegaMenu\Model\Export\Source\MenuTemplate::class);
			$this->collection->addItem($menuTemplateAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $idTempAttribute */
			$idTempAttribute = $this->attributeFactory->create();
			$idTempAttribute->setId('id_temp');
			$idTempAttribute->setBackendType('int');
			$idTempAttribute->setDefaultFrontendLabel('id_temp');
			$idTempAttribute->setAttributeCode('id_temp');
			$this->collection->addItem($idTempAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $customCssAttribute */
			$customCssAttribute = $this->attributeFactory->create();
			$customCssAttribute->setId('custom_css');
			$customCssAttribute->setBackendType('text');
			$customCssAttribute->setDefaultFrontendLabel('custom_css');
			$customCssAttribute->setAttributeCode('custom_css');
			$this->collection->addItem($customCssAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $menuTopAttribute */
			$menuTopAttribute = $this->attributeFactory->create();
			$menuTopAttribute->setId('menu_top');
			$menuTopAttribute->setBackendType('text');
			$menuTopAttribute->setDefaultFrontendLabel('menu_top');
			$menuTopAttribute->setAttributeCode('menu_top');
			$this->collection->addItem($menuTopAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $menuAliasAttribute */
			$menuAliasAttribute = $this->attributeFactory->create();
			$menuAliasAttribute->setId('menu_alias');
			$menuAliasAttribute->setBackendType('text');
			$menuAliasAttribute->setDefaultFrontendLabel('menu_alias');
			$menuAliasAttribute->setAttributeCode('menu_alias');
			$this->collection->addItem($menuAliasAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $eventAttribute */
			$eventAttribute = $this->attributeFactory->create();
			$eventAttribute->setId('event');
			$eventAttribute->setBackendType('text');
			$eventAttribute->setDefaultFrontendLabel('event');
			$eventAttribute->setAttributeCode('event');
			$eventAttribute->setFrontendInput('select');
			$eventAttribute->setSourceModel(\Magenest\MegaMenu\Model\Export\Source\Event::class);
			$this->collection->addItem($eventAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $scrollToFixedAttribute */
			$scrollToFixedAttribute = $this->attributeFactory->create();
			$scrollToFixedAttribute->setId('scroll_to_fixed');
			$scrollToFixedAttribute->setBackendType('text');
			$scrollToFixedAttribute->setDefaultFrontendLabel('scroll_to_fixed');
			$scrollToFixedAttribute->setAttributeCode('scroll_to_fixed');
			$scrollToFixedAttribute->setFrontendInput('select');
			$scrollToFixedAttribute->setSourceModel(\Magenest\MegaMenu\Model\Export\Source\ScrollToFixed::class);
			$this->collection->addItem($scrollToFixedAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $mobileTemplateAttribute */
			$mobileTemplateAttribute = $this->attributeFactory->create();
			$mobileTemplateAttribute->setId('mobile_template');
			$mobileTemplateAttribute->setBackendType('text');
			$mobileTemplateAttribute->setDefaultFrontendLabel('mobile_template');
			$mobileTemplateAttribute->setAttributeCode('mobile_template');
			$mobileTemplateAttribute->setFrontendInput('select');
			$mobileTemplateAttribute->setSourceModel(\Magenest\MegaMenu\Model\Export\Source\MobileTemplate::class);
			$this->collection->addItem($mobileTemplateAttribute);

			/** @var \Magento\Eav\Model\Entity\Attribute $disableIblocksAttribute */
			$disableIblocksAttribute = $this->attributeFactory->create();
			$disableIblocksAttribute->setId('disable_iblocks');
			$disableIblocksAttribute->setBackendType('text');
			$disableIblocksAttribute->setDefaultFrontendLabel('disable_iblocks');
			$disableIblocksAttribute->setAttributeCode('disable_iblocks');
			$disableIblocksAttribute->setFrontendInput('select');
			$disableIblocksAttribute->setSourceModel(\Magenest\MegaMenu\Model\Export\Source\DisableIblocks::class);
			$this->collection->addItem($disableIblocksAttribute);
        }

        return $this->collection;
    }
}
