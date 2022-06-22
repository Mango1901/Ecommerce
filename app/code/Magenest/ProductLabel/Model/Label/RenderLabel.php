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

namespace Magenest\ProductLabel\Model\Label;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magenest\ProductLabel\Api\Data\ConstantInterface;
use Magenest\ProductLabel\Model\Label;
use Magento\Framework\DataObject\IdentityInterface;

class RenderLabel
{

    const CATEGORY_PAGE = 'category_data';

    const PRODUCT_PAGE = 'product_data';

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelIndex
     */
    private $_labelIndex;

    /**
     * @var \Magenest\ProductLabel\Api\LabelRepositoryInterface
     */
    private $_labelRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    private $layout;

    /**
     * @var Configurable
     */
    private $productConfigurable;

    /**
     * @var null|\Magenest\ProductLabel\Block\Label
     */
    private $labelBlock = null;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * RenderLabel constructor.
     * @param \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex
     * @param \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param Configurable $productConfigurable
     * @param \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magenest\ProductLabel\Model\ResourceModel\LabelIndex $labelIndex,
        \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\View\LayoutInterface $layout,
        Configurable $productConfigurable,
        \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory
    ) {
        $this->_labelIndex = $labelIndex;
        $this->_labelRepository = $labelRepository;
        $this->customerSession = $session;
        $this->layout = $layout;
        $this->productConfigurable = $productConfigurable;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param Product $product
     * @param $page
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function renderProductLabel(Product $product, $page)
    {
        $html = '';
        $labels = [];
        $customerGroupId = $this->customerSession->getCustomerGroupId();
        $labelIds = $this->getLabelIds($product->getId(), $product->getStoreId(), $customerGroupId);
        if ($product->getTypeId() == Configurable::TYPE_CODE) {
            $usedProducts = $this->productConfigurable->getUsedProducts($product);
            foreach ($usedProducts as $usedProduct) {
                $usedProductLabelIds = $this->getLabelIds($usedProduct->getId(), $usedProduct->getStoreId(), $customerGroupId);
                foreach ($usedProductLabelIds as $usedProductLabelId) {
                    if (!in_array($usedProductLabelId, $labelIds)) {
                        $labelIds[] = $usedProductLabelId;
                    }
                }
            }
        }
        foreach ($labelIds as $labelId) {
            $label = $this->_labelRepository->get($labelId);
            $priority = $label->getPriority();
            $data = $label->getData($page);
            $label->setData('display', isset($data['display']) ? $data['display'] : '');
            $position = $data['position'];
            if (isset($labels[$position]['priority']) && $priority > $labels[$position]['priority']) {
                break;
            }
            $labels[$position] = ['priority' => $priority, 'label' => $label];
        }

        foreach ($labels as $index => $dataLabel) {
            $label = $dataLabel['label'];
            $display = $label->getData('display');
            if ($display == ConstantInterface::DISPLAY_LABEL) {
                $html .= $this->generateHtml($label, $page, $product);
            }
        }
        return $html;
    }

    /**
     * @param $productId
     * @param $storeId
     * @param $customerGroupId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLabelIds($productId, $storeId, $customerGroupId)
    {
        $idsLabelAllStore = $this->_labelIndex->getLabelIds($productId, 0, $customerGroupId);
        $ids = $this->_labelIndex->getLabelIds($productId, $storeId, $customerGroupId);
        $array = array_merge($idsLabelAllStore, $ids);
        return array_unique($array);
    }

    /**
     * @param $label
     * @param $page
     * @param $product
     * @return string
     */
    public function generateHtml($label, $page, $product)
    {
        $this->labelBlock = $this->layout->createBlock(\Magenest\ProductLabel\Block\Label::class)
            ->setData('label_object', $label)
            ->setData('page', $page)
            ->setData('product', $product);

        return $this->labelBlock->toHtml();
    }
}
