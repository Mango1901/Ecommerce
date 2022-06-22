<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Model;

class Label extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'megenest_menu_label';

    protected $_cacheTag = 'megenest_menu_label';

    protected $_eventPrefix = 'megenest_menu_label';

    protected $_validator;

    /**
     * Label constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Label\Validator $validator
     * @param ResourceModel\Label $resource
     * @param ResourceModel\Label\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\MegaMenu\Model\Label\Validator $validator,
        \Magenest\MegaMenu\Model\ResourceModel\Label $resource,
        \Magenest\MegaMenu\Model\ResourceModel\Label\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_validator = $validator;
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    protected function _construct()
    {
        $this->_init(\Magenest\MegaMenu\Model\ResourceModel\Label::class);
    }

    protected function _getValidationRulesBeforeSave()
    {
        return $this->_validator;
    }
}
