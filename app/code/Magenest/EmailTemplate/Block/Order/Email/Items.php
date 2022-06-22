<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\EmailTemplate\Block\Order\Email;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Sales Order Email items.
 *
 * @api
 * @since 100.0.2
 */
class Items extends \Magento\Sales\Block\Order\Email\Items
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        Context $context,
        ProductRepositoryInterface $productRepository,
        array $data = [],
        ?OrderRepositoryInterface $orderRepository = null
    ) {
        $this->productRepository = $productRepository;
        parent::__construct($context, $data, $orderRepository);
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getProductReviewUrl($order)
    {
        if (count($order->getItems())) {
            $product = $this->productRepository->getById(current($order->getItems())->getProductId());
            return $product->getProductUrl();
        }

        return null;
    }
}
