<?php

namespace Magenest\AffiliateOpt\Model;

use Magenest\AffiliateOpt\Api\OrderRepositoryInterface;
use Magenest\AffiliateOpt\Model\AffiliateFactory;
use Magenest\AffiliateOpt\Model\AffiliateItemFactory;

/**
 * Class OrderRepository
 * @package Magenest\AffiliateOpt\Model
 */
class OrderRepository implements OrderRepositoryInterface
{
    /**
     * @var AffiliateFactory
     */
    protected $affiliateFactory;

    /**
     * @var \Magenest\AffiliateOpt\Model\AffiliateFactory
     */
    protected $affiliateItemFactory;

    /**
     * OrderRepository constructor.
     *
     * @param \Magenest\AffiliateOpt\Model\AffiliateFactory $affiliateFactory
     * @param \Magenest\AffiliateOpt\Model\AffiliateItemFactory $affiliateItemFactory
     */
    public function __construct(
        AffiliateFactory $affiliateFactory,
        AffiliateItemFactory $affiliateItemFactory
    ) {
        $this->affiliateFactory = $affiliateFactory;
        $this->affiliateItemFactory = $affiliateItemFactory;
    }

    /**
     * {@inheritDoc}
     */
    public function get($orderId)
    {
        return $this->affiliateFactory->create()->load($orderId);
    }

    /**
     * {@inheritDoc}
     */
    public function getItemById($itemId)
    {
        return $this->affiliateItemFactory->create()->load($itemId);
    }
}
