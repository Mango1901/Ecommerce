<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 13/12/2021
 * Time: 08:47
 */

namespace Magenest\CustomCheckout\Model\Cart;

use Magento\Catalog\Helper\Product\ConfigurationPool;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\Data\TotalsItemInterfaceFactory;
use Magento\Quote\Model\Cart\Totals\ItemConverter;

class CustomItemConverter extends ItemConverter
{
    /**
     * @var ConfigurationPool
     */
    private $configurationPool;

    /**
     * @var EventManager
     */
    private $eventManager;

    /**
     * @var TotalsItemInterfaceFactory
     */
    private $totalsItemFactory;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * Constructs a totals item converter object.
     *
     * @param ConfigurationPool $configurationPool
     * @param EventManager $eventManager
     * @param TotalsItemInterfaceFactory $totalsItemFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param Json|null $serializer
     * @throws \RuntimeException
     */
    public function __construct(
        ConfigurationPool $configurationPool,
        EventManager $eventManager,
        TotalsItemInterfaceFactory $totalsItemFactory,
        DataObjectHelper $dataObjectHelper,
        Json $serializer = null
    ) {
        $this->configurationPool = $configurationPool;
        $this->eventManager = $eventManager;
        $this->totalsItemFactory = $totalsItemFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(Json::class);
        parent::__construct(
            $configurationPool,
            $eventManager,
            $totalsItemFactory,
            $dataObjectHelper,
            $serializer
        );
    }

    /**
     * Converts a specified quote item model to a totals item data object.
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return \Magento\Quote\Api\Data\TotalsItemInterface
     * @throws \Exception
     */
    public function modelToDataObject($item)
    {
        $this->eventManager->dispatch('items_additional_data', ['item' => $item]);
        $items = $item->toArray();
        $items['options'] = $this->getFormattedOptionValue($item);
        unset($items[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);

        $itemsData = $this->totalsItemFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $itemsData,
            $items,
            \Magento\Quote\Api\Data\TotalsItemInterface::class
        );
        $originalPrice = $item->getProduct()->getPrice();
        if ($originalPrice <= $item->getPrice()) {
            $originalPrice = 0;
        }
        $extension = $itemsData->getExtensionAttributes();
        $extension->setProductPrice($originalPrice*$itemsData->getQty());
        $itemsData->setExtensionAttributes($extension);
        return $itemsData;
    }

    /**
     * Retrieve formatted item options view
     *
     * @param \Magento\Quote\Api\Data\CartItemInterface $item
     * @return string
     */
    private function getFormattedOptionValue($item)
    {
        $optionsData = [];

        /* @var $helper \Magento\Catalog\Helper\Product\Configuration */
        $helper = $this->configurationPool->getByProductType('default');

        $options = $this->configurationPool->getByProductType($item->getProductType())->getOptions($item);
        foreach ($options as $index => $optionValue) {
            $params = [
                'max_length' => 55,
                'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
            ];
            $option = $helper->getFormattedOptionValue($optionValue, $params);
            $optionsData[$index] = $option;
            $optionsData[$index]['label'] = $optionValue['label'];
        }
        return $this->serializer->serialize($optionsData);
    }
}
