<?php

namespace Magenest\AffiliateOpt\Block\Account\Home;

use Exception;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\NoSuchEntityException;
use Zend_Db_Expr;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magenest\Affiliate\Block\Account\Home\Transaction;
use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magenest\Affiliate\Helper\Payment;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magenest\AffiliateOpt\Helper\Reports as ReportsHelper;
use Magento\Framework\App\RequestInterface;
use Magenest\Affiliate\Model\ResourceModel\Transaction\CollectionFactory;
use Magenest\Affiliate\Model\Transaction\Status;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class CreditChart
 * @package Magenest\AffiliateOpt\Block\Account\Home
 */
class CreditChart extends Transaction
{
    const COMPONENT_NAME = 'credit-chart';

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ReportsHelper
     */
    private $reportsHelper;

    /**
     * @var CollectionFactory
     */
    private $collection;

    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * CreditChart constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param View $helperView
     * @param AffiliateHelper $affiliateHelper
     * @param Payment $paymentHelper
     * @param JsonHelper $jsonHelper
     * @param Registry $registry
     * @param PriceHelper $pricingHelper
     * @param ObjectManagerInterface $objectManager
     * @param CampaignFactory $campaignFactory
     * @param AccountFactory $accountFactory
     * @param WithdrawFactory $withdrawFactory
     * @param TransactionFactory $transactionFactory
     * @param RequestInterface $request
     * @param ReportsHelper $reportsHelper
     * @param CollectionFactory $collection
     * @param PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        View $helperView,
        AffiliateHelper $affiliateHelper,
        Payment $paymentHelper,
        JsonHelper $jsonHelper,
        Registry $registry,
        PriceHelper $pricingHelper,
        ObjectManagerInterface $objectManager,
        CampaignFactory $campaignFactory,
        AccountFactory $accountFactory,
        WithdrawFactory $withdrawFactory,
        TransactionFactory $transactionFactory,
        RequestInterface $request,
        ReportsHelper $reportsHelper,
        CollectionFactory $collection,
        PriceCurrencyInterface $priceCurrency,
        array $data = []
    ) {
        $this->request       = $request;
        $this->reportsHelper = $reportsHelper;
        $this->collection    = $collection;
        $this->priceCurrency = $priceCurrency;

        parent::__construct(
            $context,
            $customerSession,
            $helperView,
            $affiliateHelper,
            $paymentHelper,
            $jsonHelper,
            $registry,
            $pricingHelper,
            $objectManager,
            $campaignFactory,
            $accountFactory,
            $withdrawFactory,
            $transactionFactory,
            $data
        );
    }

    /**
     * @return string
     */
    public function getComponentName()
    {
        return static::COMPONENT_NAME;
    }

    /**
     * @return array[]
     */
    public function getSelectedMonthNumber()
    {
        return [
            ['value' => 6, 'label' => __('6 Months Ago')],
            ['value' => 12, 'label' => __('1 Year Ago')],
            ['value' => 24, 'label' => __('2 Years Ago')],
            ['value' => 36, 'label' => __('3 Years Ago')]
        ];
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     * @throws Exception
     */
    public function creditChartData()
    {
        $date              = $this->getDateRange();
        $monthNumber       = $this->request->getParam('month') ?: 6;
        $monthNumber       = $monthNumber === 'all' ? $this->getMonthDiff() : $monthNumber;
        $creditBalanceData = $this->getCreditBalanceData($date[0], $date[1]);
        $months            = array_reverse($this->reportsHelper->getDateMonths((int)$monthNumber));

        $creditChartData = [];

        foreach ($months as $index => $month) {
            if (isset($creditChartData['balance'][$index])) {
                break;
            }
            $creditChartData['period'][]  = $month;
            $creditChartData['balance'][] = 0;
            if (!empty($creditBalanceData)) {
                foreach ($creditBalanceData['period'] as $key => $period) {
                    if ($month === $period) {
                        $creditChartData['balance'][$index] = $this->priceCurrency->convert(
                            $creditBalanceData['balance'][$key]
                        );
                        break;
                    }
                }
            }

        }

        $borderColor     = $this->getAffiliateHelper()->getModuleConfig('credit_balance_chart/border_color');
        $backgroundColor = $this->getAffiliateHelper()->getModuleConfig('credit_balance_chart/background_color');

        return ReportsHelper::jsonEncode([
            'type'                => 'line',
            'labelColor'          => ['labels' => $creditChartData['period'], 'colors' => $backgroundColor],
            'borderColor'         => $borderColor,
            'data'                => $creditChartData['balance'],
            'compareData'         => false,
            'isCompare'           => false,
            'priceFormat'         => $this->reportsHelper->getPriceFormat(),
            'maintainAspectRatio' => false,
            'datasetLabel'        => __('Balance'),
            'position'            => 'top'
        ]);
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getDateRange()
    {
        $startDate = '-6 month';
        if ($month = $this->request->getParam('month')) {
            $month     = $month === 'all' ? $this->getMonthDiff() : $month;
            $startDate = '-' . $month . ' month';
        }

        list($startDate, $endDate) = $this->reportsHelper->getDateTimeRangeFormat(
            $startDate,
            'now'
        );

        return [$startDate, $endDate];
    }

    /**
     * @return int
     * @throws Exception
     */
    public function getMonthDiff()
    {
        if ($customer = $this->getCustomer()) {
            $transactionCollection = $this->collection->create()
                ->addFieldToFilter('updated_at', ['lteq' => $this->reportsHelper->getDateTimeNow()])
                ->addFieldToFilter('customer_id', ['eq' => $customer->getId()])
                ->addFieldToFilter('status', ['eq' => Status::STATUS_COMPLETED]);

            $select     = $transactionCollection->getSelect();
            $connection = $transactionCollection->getConnection();
            $select->reset(Select::COLUMNS);
            $select->columns(
                [
                    'max_date' => new Zend_Db_Expr(
                        sprintf(
                            'MAX(main_table.updated_at)'
                        )
                    ),
                    'min_date' => new Zend_Db_Expr(
                        sprintf(
                            'MIN(main_table.updated_at)'
                        )
                    )
                ]
            );
            $select->group(['customer_id']);
            $result = $transactionCollection->getConnection()->select()->from(['date_table' => $select], [
                'date_diff' => new \Zend_Db_Expr('TIMESTAMPDIFF(month, date_table.min_date, date_table.max_date)')
            ]);

            return (int)$connection->fetchOne($result) + 2;
        }

        return 0;
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return array
     */
    public function getCreditBalanceData($from, $to)
    {
        if ($customer = $this->getCustomer()) {
            $transactionCollection = $this->collection->create()->addFieldToFilter('updated_at', ['gteq' => $from])
                ->addFieldToSelect(['updated_at'])
                ->addFieldToFilter('updated_at', ['lteq' => $to])
                ->addFieldToFilter('customer_id', ['eq' => $customer->getId()])
                ->addFieldToFilter('status', ['eq' => Status::STATUS_COMPLETED]);

            $select     = $transactionCollection->getSelect();
            $connection = $transactionCollection->getConnection();

            $select->reset(Select::COLUMNS);
            $select->columns(
                [
                    'period'  => sprintf(
                        '%s',
                        $connection->getDateFormatSql('main_table.updated_at', '%Y-%m')
                    ),
                    'balance' => new Zend_Db_Expr(
                        sprintf(
                            'SUM(IF(main_table.amount >=0, main_table.amount, 0))'
                        )
                    ),
                ]
            );
            $select->group(['period']);

            if (!empty($items = $connection->fetchAll($select))) {
                $data = [];
                foreach ($items as $key => $item) {
                    $data['period'][]  = $item['period'];
                    $data['balance'][] = $item['balance'];
                }

                return $data;
            }
        }

        return [];
    }
}
