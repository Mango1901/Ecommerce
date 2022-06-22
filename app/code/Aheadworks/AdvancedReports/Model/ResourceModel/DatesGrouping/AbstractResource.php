<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Magento\Framework\App\DeploymentConfig;
use Aheadworks\AdvancedReports\Model\Config;

/**
 * Class AbstractResource
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\DatesGrouping
 */
abstract class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var int
     */
    const LIMIT = 1000;

    /**
     * @var string
     */
    private $firstDate;

    /**
     * @var DeploymentConfig
     */
    private $deploymentConfig;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Context $context
     * @param DeploymentConfig $deploymentConfig
     * @param Config $config
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DeploymentConfig $deploymentConfig,
        Config $config,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->deploymentConfig = $deploymentConfig;
        $this->config = $config;
    }

    /**
     * Update table
     *
     * @return $this
     */
    abstract public function updateTable();

    /**
     * Insert periods into table
     *
     * @param string $table
     * @param [] $periods
     * @return $this
     */
    protected function addPeriodToTable($table, $periods)
    {
        $offset = 0;
        while ($offset < count($periods)) {
            $intervalsPart = array_slice($periods, $offset, self::LIMIT);
            $this->getConnection()->insertMultiple($table, $intervalsPart);
            $offset += self::LIMIT;
        }
        return $this;
    }

    /**
     * Retrieve first date from magento
     *
     * @return string
     */
    protected function getFirstDate()
    {
        if (!$this->firstDate) {
            $installMagentoDate = strtotime(
                $this->deploymentConfig->get(ConfigOptionsListConstants::CONFIG_PATH_INSTALL_DATE)
            );
            $productCreateDate = $this->getConnection()->fetchOne(
                'SELECT MIN(created_at) FROM '
                . $this->getTable('catalog_product_entity')
            );
            $customerCreateDate = $this->getConnection()->fetchOne(
                'SELECT MIN(created_at) FROM '
                . $this->getTable('customer_entity')
            );
            $orderCreateDate = $this->getConnection()->fetchOne(
                'SELECT MIN(created_at) FROM '
                . $this->getTable('sales_order')
            );

            $installDate = new \DateTime();
            $installDate->setTimestamp($installMagentoDate);
            $productDate = new \DateTime($productCreateDate);
            $customerDate = new \DateTime($customerCreateDate);
            $orderDate = new \DateTime($orderCreateDate);
            $this->firstDate = min(
                [
                    $installDate->format('Y-m-d'),
                    $productDate->format('Y-m-d'),
                    $customerDate->format('Y-m-d'),
                    $orderDate->format('Y-m-d')
                ]
            );
        }
        return $this->firstDate;
    }

    /**
     * Retrieve from date of $maxDateStr
     *
     * @param string $maxDateStr
     * @return \DateTime
     */
    protected function getFromDate($maxDateStr)
    {
        $fromDate = new \DateTime($maxDateStr);
        if (!$maxDateStr) {
            $fromDate = new \DateTime($this->getFirstDate());
        }
        return $fromDate->setTime(0, 0, 0);
    }

    /**
     * Retrieve to date of plus 1 year
     *
     * @return \DateTime
     */
    protected function getToDate()
    {
        $toDate = new \DateTime();
        return $toDate->modify('+1 year')->setTime(0, 0, 0);
    }
}
