<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MobileApi\Model\Catalog;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magenest\MobileApi\Model\Catalog\Widget\ProductSlider;

/**
 * Class WidgetAbstract
 * @package Magenest\MobileApi\Model\Catalog
 */
abstract class WidgetAbstract
{
    /**
     * @var ReadExtensions
     */
    protected $_readExtensions;

    /**
     * @var JoinProcessorInterface
     */
    protected $_extensionAttributesJoinProcessor;

    /**
     * @var  array
     */
    protected $_data = [];

    /**
     * Constructor.
     *
     * @param ReadExtensions $readExtensions
     * @param JoinProcessorInterface $joinProcessor
     * @param array $data
     */
    function __construct(
        ReadExtensions $readExtensions,
        JoinProcessorInterface $joinProcessor,
        array $data
    )
    {
        $this->_data = $data;
        $this->_extensionAttributesJoinProcessor = $joinProcessor;
        $this->_readExtensions = $readExtensions ?: ObjectManager::getInstance()->get(ReadExtensions::class);
    }

    /**
     * Render widget data
     *
     * @return ProductSlider|false
     */
    abstract function render();

    /**
     * Process Collection
     * @param Collection $collection
     */
    public function processCollection(Collection &$collection)
    {
        $this->_extensionAttributesJoinProcessor->process($collection);
        $collection->addAttributeToSelect('*');
        $this->_addExtensionAttributes($collection);
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Add extension attributes to loaded items.
     *
     * @param Collection $collection
     * @return Collection
     */
    protected function _addExtensionAttributes(Collection $collection)
    {
        foreach ($collection->getItems() as $item) {
            $this->_readExtensions->execute($item);
        }

        return $collection;
    }
}