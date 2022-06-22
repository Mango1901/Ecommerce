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

namespace Magenest\ProductLabel\Model;

use Magenest\ProductLabel\Model\ResourceModel\LabelProduct as ResourceEvent;
use Magenest\ProductLabel\Model\ResourceModel\LabelProduct\Collection as Collection;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Event
 * @package Magenest\GiftRegistry\Model
 */
class LabelProduct extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'magenest_product_label_option_product';

    /**
     * @var string
     */
    protected $_eventPrefix = 'event';

    /**
     * Event constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceEvent $resource
     * @param Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ResourceEvent $resource,
        Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
