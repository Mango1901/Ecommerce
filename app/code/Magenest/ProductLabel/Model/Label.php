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

use Magento\Rule\Model\AbstractModel;
use Magento\Framework\Api\AttributeValueFactory;
use Magenest\ProductLabel\Api\Data\LabelInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Api\ExtensionAttributesFactory;

/**
 * Class Label
 * @package Magenest\ProductLabel\Model
 */
class Label extends AbstractModel implements LabelInterface, IdentityInterface
{
    /**
     * cache tag
     */
    const CACHE_TAG = 'product_label';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magenest\ProductLabel\Model\Rule\Condition\CombineFactory
     */
    protected $combineFactory;

    /**
     * @var \Magento\CatalogRule\Model\Rule\Action\CollectionFactory
     */
    protected $actionCollectionFactory;

    /**
     * @var \Magento\Framework\Model\ResourceModel\Iterator
     */
    protected $_resourceIterator;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Store matched product Ids
     *
     * @var array
     */
    protected $_productIds;

    /**
     * Limitation for products collection
     *
     * @var int|array|null
     */
    protected $_productsFilter = null;

    /**
     * Label constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param Rule\Condition\CombineFactory $combineFactory
     * @param \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\Iterator $iterator
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     * @param ExtensionAttributesFactory|null $extensionFactory
     * @param AttributeValueFactory|null $customAttributeFactory
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magenest\ProductLabel\Model\Rule\Condition\CombineFactory $combineFactory,
        \Magento\CatalogRule\Model\Rule\Action\CollectionFactory $actionCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\Iterator $iterator,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = [],
        ExtensionAttributesFactory $extensionFactory = null,
        AttributeValueFactory $customAttributeFactory = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null
    ) {
        $this->combineFactory = $combineFactory;
        $this->actionCollectionFactory = $actionCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_resourceIterator = $iterator;
        $this->customerSession = $customerSession;
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data, $extensionFactory, $customAttributeFactory, $serializer);
    }

    protected function _construct()
    {
        $this->_init('\Magenest\ProductLabel\Model\ResourceModel\Label');
    }

    /**
     * @param null $ids
     * @param null $matchedProductIds
     * @return array|null
     */
    public function getLabelMatchingProductIds($ids = null)
    {
        if ($this->getData('conditions_serialized') !== '') {
            $this->setConditions([]);
            $this->setStoreId($this->getData('store_id'));
            $this->setConditionsSerialized($this->getData('conditions_serialized'));
            if ($ids) {
                $this->setProductFilter($ids);
            }

            return $this->getMatchingProductIdsByLabel();
        }

        return null;
    }

    /**
     * Filtering products that must be checked for matching with rule
     *
     * @param  int|array $productIds
     * @return void
     */
    public function setProductFilter($productIds)
    {
        $this->_productsFilter = $productIds;
    }

    /**
     * @param null $productId
     * @return array
     */
    public function getMatchingProductIdsByLabel()
    {
        if ($this->_productIds === null) {
            $this->_productIds = [];
            $this->setCollectedAttributes([]);

            if ($this->getStoreId()) {
                /** @var $productCollection \Magento\Catalog\Model\ResourceModel\Product\Collection */
                $productCollection = $this->_productCollectionFactory->create()->setStoreId($this->getStoreId());
                if ($this->_productsFilter) {
                    $productCollection->addIdFilter($this->_productsFilter);
                }
                $this->getConditions()->collectValidatedAttributes($productCollection);

                $this->_resourceIterator->walk(
                    $productCollection->getSelect(),
                    [[$this, 'callbackValidateProduct']],
                    [
                        'attributes' => $this->getCollectedAttributes(),
                        'product' => $this->_productFactory->create(),
                        'store_id' => $this->getStoreId(),
                        'label' => $this
                    ]
                );
            }
        }

        return $this->_productIds;
    }

    /**
     * @param array $args
     */
    public function callbackValidateProduct($args)
    {
        $product = $args['product'];
        $storeIds = $args['store_id'];
        $product->setData($args['row']);

        foreach ($storeIds as $storeId) {
            $product->setStoreId($storeId);
            $result = $this->getConditions()->validate($product);
            if($result) {
                $this->_productIds[$product->getId()][$storeId] = true;
            }
        }
    }

    /**
     * @param $label
     * @return bool
     */
    public function isInActiveDate()
    {
        $activeDay = strtotime($this->getFromDate());
        $expiredDay = strtotime($this->getToDate());
        $today = strtotime(date("m/d/Y"));

        if ((!$activeDay && !$expiredDay) || (!$activeDay && $today <= $expiredDay) ||
            ($today >= $activeDay && !$expiredDay) || ($today >= $activeDay && $today <= $expiredDay)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_getData(LabelInterface::LABEL_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData(LabelInterface::LABEL_ID, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_getData(LabelInterface::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        return $this->setData(LabelInterface::NAME, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->_getData(LabelInterface::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(LabelInterface::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->_getData(LabelInterface::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        return $this->setData(LabelInterface::TYPE, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return $this->_getData(LabelInterface::CONDITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCondition($condition)
    {
        return $this->setData(LabelInterface::CONDITION, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return $this->_getData(LabelInterface::PRIORITY);
    }

    /**
     * {@inheritdoc}
     */
    public function setPriority($priority)
    {
        return $this->setData(LabelInterface::PRIORITY, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function getFromDate()
    {
        return $this->_getData(LabelInterface::FROM_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFromDate($fromDate)
    {
        return $this->setData(LabelInterface::FROM_DATE, $fromDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getToDate()
    {
        return $this->_getData(LabelInterface::TO_DATE);
    }

    /**
     * {@inheritdoc}
     */
    public function setToDate($toDate)
    {
        return $this->setData(LabelInterface::TO_DATE, $toDate);
    }

    /**
     * {@inheritdoc}
     */
    public function getConditionsInstance()
    {
        return $this->combineFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getActionsInstance()
    {
        return $this->actionCollectionFactory->create();
    }

    /**
     * @param string $formName
     * @return string
     */
    public function getConditionsFieldSetId($formName = '')
    {
        return $formName . 'rule_conditions_fieldset_' . $this->getId();
    }

    /**
     * Get current product from registry
     *
     * @return \Magento\Catalog\Api\Data\ProductInterface|null
     */
    public function getProduct()
    {
        if (!$this->_registry) {
            return null;
        }

        return $this->_registry->registry('product');
    }

    /**
     * Receive page store ids
     *
     * @return int[]
     */
    public function getStores()
    {
        $stores = $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');

        if (!is_array($stores)) {
            $stores = (array) $stores;
        }
        return $stores;
    }

    /**
     * Get current customer from customer session
     *
     * @return \Magento\Customer\Model\Customer|null
     */
    public function getCustomer()
    {
        if (!$this->customerSession) {
            return null;
        }

        return $this->customerSession->getCustomer();
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        $identities = [self::CACHE_TAG . '_' . $this->getId()];
        if (!$this->getId() || $this->isDeleted() || $this->dataHasChangedFor($this->getData('label_id')) || $this->isSaveAllowed()) {
            $identities[] = self::CACHE_TAG;
        }
        return array_unique($identities);
    }
}
