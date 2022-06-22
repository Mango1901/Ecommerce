<?php


namespace Magenest\Affiliate\Model\Transaction;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Affiliate\Helper\Data as DataHelper;
use Magenest\Affiliate\Model\TransactionFactory;

/**
 * Class AbstractAction
 * @package Magenest\Affiliate\Model\Transaction
 * @method getAccount()
 * @method getCode()
 * @method getExtraContent()
 */
abstract class AbstractAction extends DataObject
{
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    /**
     * @var TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * AbstractAction constructor.
     *
     * @param JsonHelper $jsonHelper
     * @param StoreManagerInterface $storeManager
     * @param DataHelper $dataHelper
     * @param TransactionFactory $transactionFactory
     * @param DateTime $dateTime
     * @param array $data
     */
    public function __construct(
        JsonHelper $jsonHelper,
        StoreManagerInterface $storeManager,
        DataHelper $dataHelper,
        TransactionFactory $transactionFactory,
        DateTime $dateTime,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->storeManager = $storeManager;
        $this->dataHelper = $dataHelper;
        $this->transactionFactory = $transactionFactory;
        $this->dateTime = $dateTime;

        parent::__construct($data);
    }

    /**
     * @return mixed
     */
    abstract public function getAmount();

    /**
     * @param null $transaction
     *
     * @return mixed
     */
    abstract public function getTitle($transaction = null);

    /**
     * @return mixed
     */
    abstract public function getType();

    /**
     * Prepare transaction data
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function prepareTransaction()
    {
        $defaultData = [
            'account_id' => $this->getAccount()->getId(),
            'customer_id' => $this->getAccount()->getCustomerId(),
            'action' => $this->getCode(),
            'type' => $this->getType(),
            'amount' => $this->getAmount(),
            'current_balance' => $this->getAccount()->getBalance(),
            'status' => $this->getStatus(),
            'store_id' => $this->storeManager->getStore()->getId(),
            'title' => $this->getTitle(),
            'extra_content' => $this->getAdditionContent(),
        ];

        return array_merge($defaultData, $this->prepareAction());
    }

    /**
     * @return array
     */
    public function prepareAction()
    {
        return [];
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return Status::STATUS_COMPLETED;
    }

    /**
     * Get extra content of transaction
     *
     * @return string
     */
    public function getAdditionContent()
    {
        $extraContent = $this->getExtraContent();
        if (!is_array($extraContent)) {
            return null;
        }

        return $this->jsonHelper->jsonEncode($extraContent);
    }

    /**
     * Holding date of transaction. Transaction will be completed when holding date is reached
     *
     * @return int
     */
    public function getHoldDays()
    {
        $holdDays = (int)$this->dataHelper->getCommissionHoldingDays($this->getOrder()->getStoreId());

        return $holdDays;
    }

    /**
     * @param $days
     *
     * @return false|null|string
     */
    public function getHoldingDate($days)
    {
        if ($days <= 0) {
            return null;
        }

        return date('Y-m-d H:i:s', strtotime($this->dateTime->gmtDate()) + $days * 86400);
    }
}
