<?php

namespace Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as ItemFactory;
use Magento\Store\Model\Store;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\Exception\LocalizedException;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\ResourceModel\Transaction\CollectionFactory;
use Magenest\AffiliateOpt\Helper\Reports as ReportsHelper;
use Magenest\AffiliateOpt\Model\ResourceModel\Bestsellers\Grid\Collection as Bestsellers;
use Magenest\AffiliateOpt\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * Class Reports
 * @package Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard
 */
class Reports extends Template
{
    const MAGE_REPORT_CLASS = Transaction::class;
    const COMPONENT_NAME    = 'transaction-chart';

    /**
     * @var string
     */
    protected $_template = 'Magenest_AffiliateOpt::reports/dashboard/transaction.phtml';

    /**
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * @var ReportsHelper
     */
    protected $reportsHelper;

    /**
     * @var AccountFactory
     */
    protected $affiliateAccount;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var
     */
    protected $orderCollection;

    /**
     * @var ItemFactory
     */
    protected $itemFactory;

    /**
     * @var Status
     */
    protected $status;

    /**
     * @var Bestsellers
     */
    protected $bestSellers;

    /**
     * @var Store
     */
    protected $storeManager;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;


    /**
     * Reports constructor.
     *
     * @param Context $context
     * @param CollectionFactory $collection
     * @param ReportsHelper $reportsHelper
     * @param AccountFactory $affiliateAccount
     * @param CustomerFactory $customerFactory
     * @param ItemFactory $itemFactory
     * @param OrderCollection $orderCollection
     * @param Status $status
     * @param Bestsellers $bestsellers
     * @param Store $store
     * @param CurrencyFactory $currencyFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $collection,
        ReportsHelper $reportsHelper,
        AccountFactory $affiliateAccount,
        CustomerFactory $customerFactory,
        ItemFactory $itemFactory,
        OrderCollection $orderCollection,
        Status $status,
        Bestsellers $bestsellers,
        Store $store,
        CurrencyFactory $currencyFactory,
        array $data = []
    ) {
        $this->collection       = $collection;
        $this->reportsHelper    = $reportsHelper;
        $this->affiliateAccount = $affiliateAccount;
        $this->customerFactory  = $customerFactory;
        $this->itemFactory      = $itemFactory;
        $this->orderCollection  = $orderCollection;
        $this->status           = $status;
        $this->bestSellers      = $bestsellers;
        $this->storeManager     = $store;
        $this->currencyFactory  = $currencyFactory;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getComponentName()
    {
        return static::COMPONENT_NAME;
    }

    /**
     * @return mixed
     */
    public function getReportsHelper()
    {
        return ObjectManager::getInstance()->get('Magenest\Reports\Helper\Data');
    }

    /**
     * @return mixed
     */
    public function isEnabledChart()
    {
        return $this->getReportsHelper()->isEnabledChart();
    }

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Affiliate Reports');
    }

    /**
     * @inheritdoc
     */
    public function getContentHtml()
    {
        if (static::MAGE_REPORT_CLASS) {
            return $this->getLayout()->createBlock(static::MAGE_REPORT_CLASS)->toHtml();
        }

        return $this->toHtml();
    }

    /**
     * @return bool
     */
    public function canShowDetail()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getRate()
    {
        return '';
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function getCustomerName($id)
    {
        return $this->customerFactory->create()->load($id)->getName();
    }

    /**
     * @param $value
     *
     * @return string
     */
    public function getStatus($value)
    {
        if (isset($this->status->toOptionHash()[$value])) {
            return $this->status->toOptionHash()[$value];
        }

        return '';
    }

    /**
     * @param null $storeId
     * @return string|null
     * @throws LocalizedException
     */
    protected function getBaseCurrencyCode($storeId = null)
    {
        return $this->storeManager->load($storeId)->getBaseCurrencyCode();
    }

    /**
     * @param $price
     * @param null $storeId
     * @return string
     * @throws LocalizedException
     */
    public function formatPrice($price, $storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->storeManager->getStoreId() ?: 0;
        }
        $currencyCode = $this->getBaseCurrencyCode($storeId);
        $baseCurrency = $this->currencyFactory->create()->load($currencyCode);

        return $baseCurrency->format($price, [], false);
    }

}
