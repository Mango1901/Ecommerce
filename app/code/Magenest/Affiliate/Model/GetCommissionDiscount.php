<?php

namespace Magenest\Affiliate\Model;

use Magenest\Affiliate\Api\GetCommissionDiscountInterface;
use Magenest\Affiliate\Model\ResourceModel\CommissionDiscount\CollectionFactory;
use Psr\Log\LoggerInterface;

/**
 * Class GetCommissionDiscount
 */
class GetCommissionDiscount implements GetCommissionDiscountInterface
{
    const KEY_RESULT = [
        'customer_value',
        'customer_value_second',
        'type',
        'type_second',
        'total_value',
        'total_value_second'
    ];

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
    }

    /**
     * @inheirtDoc
     */
    public function execute($affiliateAccountId, $campaignId)
    {
        $result = [];
        foreach (self::KEY_RESULT as $key) {
            $result[$key] = 0;
        }
        try {
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('campaign_id', ['eq' => $campaignId]);
            $collection->addFieldToFilter('affiliate_account_id', ['eq' => $affiliateAccountId]);

            if ($collection->getFirstItem() && $collection->getFirstItem()->getId()) {
                $commissionDiscount = $collection->getFirstItem();
                foreach (self::KEY_RESULT as $key) {
                    $result[$key] = $commissionDiscount->getData($key);
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $result;
    }
}
