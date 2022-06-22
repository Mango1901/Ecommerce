<?php

namespace Magenest\AffiliateOpt\Model\ResourceModel\Transaction\Grid;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Exception\LocalizedException;
use Magenest\AffiliateOpt\Helper\Data;
use Magenest\AffiliateOpt\Model\ResourceModel\AbstractCollection;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Magenest\AffiliateOpt\Model\ResourceModel\Transaction\Grid
 */
class Collection extends AbstractCollection
{
    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param RequestInterface $request
     * @param Data $helperData
     * @param string $mainTable
     * @param string $resourceModel
     *
     * @throws LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        Data $helperData,
        $mainTable = 'magenest_affiliate_transaction',
        $resourceModel = '\Magenest\Affiliate\Model\ResourceModel\Transaction'
    ) {
        $this->_request = $request;
        $this->helperData = $helperData;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $request,
            $helperData,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $fields = ['status', 'created_at'];
        foreach ($fields as $field) {
            $this->addFilterToMap($field, 'main_table.' . $field);
        }
        $this->getSelect()->joinLeft(
            ['campaign' => $this->getTable('magenest_affiliate_campaign')],
            'campaign.campaign_id = main_table.campaign_id',
            ['campaign_name' => 'name']
        )->joinLeft(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            ['email']
        );
        $this->addDateToFilter()
            ->addStoreToFilter();

        return $this;
    }
}
