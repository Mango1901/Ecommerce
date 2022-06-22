<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 16/12/2021
 * Time: 08:44
 */

namespace Magenest\QuantitySold\Controller\RecentProduct;

use Magenest\RatingAttribute\Model\Constant;
use Magento\Review\Model\Review;
use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;

class Fetch extends Action
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /** @var Review */
    protected $reviewModel;

    /**
     * Fetch constructor.
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Review $reviewModel
     */
    public function __construct(
        Context $context,
        CollectionFactory $collectionFactory,
        Review $reviewModel
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->reviewModel = $reviewModel;
        parent::__construct($context);
    }

    public function execute()
    {
        $ids = $this->getRequest()->getParam('ids');
        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if ($ids) {
            $collection = $this->collectionFactory->create();
            $collection
                ->addAttributeToSelect(AddSoldQuantityAttribute::FINAL_SOLD_QTY)
                ->addAttributeToSelect(Constant::RATING_ATTRIBUTE)
                ->addFieldToFilter('entity_id', $ids);

            $data  = [];
            foreach ($collection as $product) {
                $data[$product->getId()] = [
                    'qty' => $product->getFinalSoldQty() ?? 0,
                    'reviews_summary' => ($product->getMagenestRating() ?? 0) . '%'
                ];
            }
            $result->setData([
                'success' => true,
                'data' => $data
            ]);
        } else {
            $result->setData('success', false);
        }

        return $result;
    }
}
