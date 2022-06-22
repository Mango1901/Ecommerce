<?php

namespace Magenest\AffiliateOpt\Helper;

use DateTime;
use Exception;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Store;
use Magenest\Affiliate\Model\Transaction\Status;
use Magenest\Core\Helper\AbstractData;

/**
 * Class Reports
 * @package Magenest\Affiliate\Helper
 */
class Reports extends AbstractData
{
    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var FormatInterface
     */
    protected $localFormat;

    protected $store;

    /**
     * Reports constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Status $status
     * @param FormatInterface $localFormat
     * @param Store $store
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Status $status,
        FormatInterface $localFormat,
        Store $store
    ) {
        $this->_status     = $status;
        $this->localFormat = $localFormat;
        $this->store = $store;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return array
     */
    public function getLabelColorsStatus()
    {
        return ['labels' => $this->getTransactionLabels(), 'colors' => $this->getTransactionColors()];
    }

    /**
     * @return array
     */
    public function getTransactionColors()
    {
        return ['#20a8d8', '#f8cb00', '#f86c6b'];
    }

    /**
     * @return array
     */
    public function getTransactionLabels()
    {
        return array_values($this->_status->getOptionHash());
    }

    /**
     * @return array
     */
    public function getTransactionStatusKeys()
    {
        return array_keys($this->_status->getOptionHash());
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPriceFormat()
    {
        $storeId = $this->store->getStoreId();
        $code = $this->store->load($storeId)->getBaseCurrencyCode();

        return $this->localFormat->getPriceFormat(null, $code);
    }

    /**
     * @return array|mixed
     */
    public function getTimezone()
    {
        return $this->getConfigValue('general/locale/timezone');
    }

    /**
     * @param $startDate
     * @param null $endDate
     *
     * @return array
     * @throws Exception
     */
    public function getDateTimeRangeFormat($startDate, $endDate = null)
    {
        $endDate   = (new DateTime($endDate ?: $startDate))->setTime(
            23,
            59,
            59
        );
        $startDate = (new DateTime($startDate))->setTime(00, 00, 00);

        return [$startDate->format('Y-m-d H:i:s'), $endDate->format('Y-m-d H:i:s')];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getDateTimeNow()
    {
        $now = (new DateTime('now'))->setTime(
            23,
            59,
            59
        );

        return $now->format('Y-m-d H:i:s');
    }

    /**
     * @param $monthNumber
     *
     * @return array
     * @throws Exception
     */
    public function getDateMonths($monthNumber)
    {
        $months = [];
        for ($i = 0; $i < $monthNumber; $i++) {
            $now       = new DateTime();
            $monthYear = new DateTime($now->format('Y-m'));
            $month     = 'P' . $i . 'M';
            $monthYear->sub(new \DateInterval($month));
            $months[] = $monthYear->format('Y-m');
        }

        return $months;
    }
}
