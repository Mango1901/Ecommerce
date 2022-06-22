<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class Factory
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
class Factory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var []
     */
    private $resourceModelNames = [
        Day::KEY => Day::class,
        Week::KEY => Week::class,
        Month::KEY => Month::class,
        Quarter::KEY => Quarter::class,
        Year::KEY => Year::class
    ];

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
     * @param string $key
     * @param array $data
     * @return \Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping\AbstractResource
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function create($key, array $data = [])
    {
        if (!isset($this->resourceModelNames[$key])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Key %1 doesn\'t exist in map', $key));
        }
        return $this->objectManager->create($this->resourceModelNames[$key], $data);
    }
}
