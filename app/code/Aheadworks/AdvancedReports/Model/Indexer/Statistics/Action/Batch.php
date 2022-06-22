<?php
/**
 * Created by PhpStorm.
 * User: ducquach
 * Date: 4/1/19
 * Time: 1:30 PM
 */
namespace Aheadworks\AdvancedReports\Model\Indexer\Statistics\Action;


use Aheadworks\AdvancedReports\Model\FlagFactory;
use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

use Aheadworks\AdvancedReports\Model\Indexer\Statistics\AbstractAction;
use Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\Factory as IndexerStatisticsFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Batch extends AbstractAction
{
    protected $indexerStatisticsFactory;

    protected $resourceModelNames = [
        'sales_overview' => Statistics\SalesOverview::class,
        'sales_detailed' => Statistics\SalesDetailed::class,
        'product_performance' => Statistics\ProductPerformance::class,
        'product_performance_category' => Statistics\ProductPerformanceByCategory::class,
        'product_performance_coupon_code' => Statistics\ProductPerformanceByCouponCode::class,
        'product_performance_manufacturer' => Statistics\ProductPerformanceByManufacturer::class,
        'product_variant_performance' => Statistics\ProductVariantPerformance::class,
        'sales_overview_coupon_code' => Statistics\SalesOverviewByCouponCode::class,
        'coupon_code' => Statistics\CouponCode::class,
        'payment_type' => Statistics\PaymentType::class,
        'manufacturer' => Statistics\Manufacturer::class,
        'category' => Statistics\Category::class
    ];

    public function __construct(
        Statistics\Factory $indexerStatisticsFactory,
        FlagFactory $reportsFlagFactory,
        DateTime $dateTime
    ) {
        parent::__construct($indexerStatisticsFactory, $reportsFlagFactory, $dateTime);
        $this->indexerStatisticsFactory = $indexerStatisticsFactory;
    }

    public function execute($ids)
    {
        foreach ($this->resourceModelNames as $resourceModelName) {
            $this->indexerStatisticsFactory->create($resourceModelName)
                ->setBatchIds($ids)
                ->reindexBatch();
        }
    }
}