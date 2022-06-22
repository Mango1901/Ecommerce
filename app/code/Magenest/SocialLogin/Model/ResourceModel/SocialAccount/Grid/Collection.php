<?php

namespace Magenest\SocialLogin\Model\ResourceModel\SocialAccount\Grid;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;

/**
 * Class Collection
 * @package Magenest\SocialLogin\Model\ResourceModel\SocialAccount\Grid
 */
class Collection extends SearchResult
{
    /**
     * @var \string[][]
     */
    protected $_map = [
        'fields' => [
            'created_at' => 'main_table.created_at',
        ]
    ];

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param string $mainTable
     * @param null $resourceModel
     * @param null $identifierName
     * @param null $connectionName
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        $mainTable,
        $resourceModel = null,
        $identifierName = null,
        $connectionName = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel, $identifierName, $connectionName);
    }

    /**
     * @return $this|\Magenest\SocialLogin\Model\ResourceModel\SocialAccount\Grid\Collection|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
             ->join(
                 ['c' => $this->getTable('customer_entity')],
                 'main_table.customer_id = c.entity_id',
                 [
                     'social_login_type' => 'CONCAT(UPPER(SUBSTRING(main_table.social_login_type,1,1)),LOWER(SUBSTRING(main_table.social_login_type,2)))',
                     'firstname'         => 'c.firstname',
                     'lastname'          => 'c.lastname',
                     'email'             => 'c.email'
                 ]
             );

        return $this;
    }
}