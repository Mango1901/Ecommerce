<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\AdvancedReports\Model\Indexer;

/**
 * Class Statistics
 *
 * @package Aheadworks\AdvancedReports\Model\Indexer
 */
class Statistics implements \Magento\Framework\Indexer\ActionInterface, \Magento\Framework\Mview\ActionInterface
{
    /**
     * @var Statistics\Action\Full
     */
    private $statisticsIndexerFull;

    private $statisticsIndexerBatch;

    /**
     * Statistics constructor.
     * @param Statistics\Action\Full $statisticsIndexerFull
     * @param Statistics\Action\Batch $statisticsIndexerBatch
     */
    public function __construct(
        Statistics\Action\Full $statisticsIndexerFull,
        Statistics\Action\Batch $statisticsIndexerBatch
    ) {
        $this->statisticsIndexerFull = $statisticsIndexerFull;
        $this->statisticsIndexerBatch = $statisticsIndexerBatch;
    }

    /**
     * Execute full indexation
     *
     * @return void
     */
    public function executeFull()
    {
        $this->statisticsIndexerFull->execute();
    }

    /**
     * Execute materialization on ids entities
     *
     * @param int[] $ids
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($ids)
    {
        $this->statisticsIndexerBatch->execute($ids);
    }

    /**
     * Execute partial indexation by ID list
     *
     * @param int[] $ids
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function executeList(array $ids)
    {
    }

    /**
     * Execute partial indexation by ID
     *
     * @param int $id
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function executeRow($id)
    {
    }
}
