<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * Create date grouping object
     *
     * @param string $className
     * @param array $data
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\AbstractResource
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($className, array $data = [])
    {
        $model = $this->objectManager->create($className, $data);
        if (!$model instanceof \Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\AbstractResource) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    '%1 doesn\'t extends '
                    . '\Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics\AbstractResource',
                    $className
                )
            );
        }
        return $model;
    }
}
