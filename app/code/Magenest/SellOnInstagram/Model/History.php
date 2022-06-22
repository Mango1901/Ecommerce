<?php

namespace Magenest\SellOnInstagram\Model;

use Exception;
use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Backend\Model\Auth\Session;
use Magenest\SellOnInstagram\Helper\Data;
use Magento\Framework\Model\AbstractModel;
use Magenest\SellOnInstagram\Logger\Logger;
use Magenest\SellOnInstagram\Model\ResourceModel\History\Collection;
use Magenest\SellOnInstagram\Model\ResourceModel\History as HistoryResource;

class History extends AbstractModel
{
    const HISTORY_ID = 'history_id';

    const CREATED_AT = 'created_at';

    const USER_ID = 'user_id';

    const TYPE = 'type';

    const ACTION = 'action';

    const ERROR_PRODUCTS = 'error_products';

    const EXECUTION_TIME = 'execution_time';

    const SUMMARY = 'summary';

    const SYNC_SCHEDULED_USER = 0;

    const IMPORT_IN_PROCESS = 'In Progress';

    const FEED_ID = 'feed_id';

    protected $lastId;

    /**
     * @var Session
     * @since 100.3.1
     */
    protected $session;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var Logger
     */
    private $logger;

    public function __construct(
        Context $context,
        Registry $registry,
        HistoryResource $resource,
        Collection $resourceCollection,
        Data $helper,
        Session $authSession,
        Logger $logger,
        array $data = []
    )
    {
        $this->helper = $helper;
        $this->session = $authSession;
        $this->logger = $logger;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @param ProductBatch $productBatch
     * @param false $updateSummary
     *
     * @return $this
     */
    public function updateReport(ProductBatch $productBatch, $feedId, $updateSummary = false)
    {
        try {
            if ($updateSummary) {
                $this->load($this->getLastId());
                $executionResult = $this->helper->getExecutionTime($this->getCreatedAt());
                $this->setErrorProduct($productBatch->getErrors());
                $summary = $this->helper->getSummaryStats($productBatch);
                $this->setSummary($summary);
            } else {
                $this->setUserId($this->getAdminId());
                $executionResult = self::IMPORT_IN_PROCESS;
                $this->setAction($productBatch->getAction());
            }
            $this->setFeedId($feedId);
            $this->setExecutionTime($executionResult);
            $this->save();
        } catch (Exception $e) {
            $this->logger->error("Error update history: " . $e->getMessage());
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastId()
    {
        return $this->lastId;
    }

    /**
     * @param $id
     *
     * @return $this
     */
    public function setLastId($id)
    {
        $this->lastId = $id;

        return $this;
    }

    /**
     * @return array|mixed|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @param $skus
     *
     * @return History
     */
    public function setErrorProduct($skus)
    {
        return $this->setData(self::ERROR_PRODUCTS, $skus);
    }

    /**
     * Set summary
     *
     * @param string $summary
     *
     * @return $this
     */
    public function setSummary($summary)
    {
        return $this->setData(self::SUMMARY, $summary);
    }

    /**
     * Set user id
     *
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Retrieve admin ID
     *
     * @return string
     */
    protected function getAdminId()
    {
        $userId = self::SYNC_SCHEDULED_USER;
        if ($this->session->isLoggedIn()) {
            $userId = $this->session->getUser()->getId();
        }

        return $userId;
    }

    /**
     * @param $action
     *
     * @return History
     */
    public function setAction($action)
    {
        return $this->setData(self::ACTION, $action);
    }

    /**
     * Set Execution Time
     *
     * @param string $executionTime
     *
     * @return $this
     */
    public function setExecutionTime($executionTime)
    {
        return $this->setData(self::EXECUTION_TIME, $executionTime);
    }

    /**
     * @param $feedId
     * @return History
     */
    public function setFeedId($feedId) {
        return $this->setData(self::FEED_ID, $feedId);
    }

    /**
     * @return array|mixed|null
     */
    public function getFeedId()
    {
        return $this->getData(self::FEED_ID);
    }

    /**
     * Get import history report ID
     *
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::HISTORY_ID);
    }

    /**
     * Get import history report ID
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Get import execution time
     *
     * @return string
     */
    public function getExecutionTime()
    {
        return $this->getData(self::EXECUTION_TIME);
    }

    /**
     * Get import history report summary
     *
     * @return string
     */
    public function getSummary()
    {
        return $this->getData(self::SUMMARY);
    }

    /**
     * @return array|mixed|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @return array|mixed|null
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * @return array|mixed|null
     */
    public function getErrorProducts()
    {
        return $this->getData(self::ERROR_PRODUCTS);
    }

    /**
     * Set history report ID
     *
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::HISTORY_ID, $id);
    }

    /**
     * @param $createdAt
     *
     * @return History
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @param $type
     *
     * @return History
     */
    public function setType($type)
    {
        return $this->setData(self::TYPE, $type);
    }

    /**
     * Initialize history resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(HistoryResource::class);
    }
}

