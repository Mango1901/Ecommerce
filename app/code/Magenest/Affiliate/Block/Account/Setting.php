<?php
namespace Magenest\Affiliate\Block\Account;

use Magenest\Affiliate\Block\Account;
use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magenest\Affiliate\Helper\Payment;
use Magenest\Affiliate\Model\Account\BankInfo;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

class Setting extends Account
{
    /** @var BankInfo */
    protected $bankInfo;

    /** @var string|null */
    private $baseUrl = null;

    /**
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
     * @param BankInfo $bankInfo
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
        BankInfo $bankInfo,
        array $data = []
    ) {
        $this->bankInfo = $bankInfo;
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
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Affiliate Setting'));

        return parent::_prepareLayout();
    }

    /**
     * @return mixed
     */
    public function getEmailNotification()
    {
        return $this->getCurrentAccount()->getEmailNotification();
    }

    /**
     * @return mixed
     */
    public function getBankNo()
    {
        return $this->getCurrentAccount()->getBankNo();
    }

    /**
     * @return mixed
     */
    public function getAccNo()
    {
        return $this->getCurrentAccount()->getAccNo();
    }

    /**
     * @return mixed
     */
    public function getAccountName()
    {
        return $this->getCurrentAccount()->getAccountName();
    }

    /**
     * @return array[]
     */
    public function getBankList()
    {
        return $this->bankInfo->toOptionArray();
    }

    /**
     * @return string
     */
    public function getTelephone()
    {
        return $this->getCurrentAccount()->getTelephone();
    }

    /**
     * @return string
     */
    public function getEmployeeId()
    {
        return $this->getCurrentAccount()->getEmployeeId();
    }

    /**
     * @return int
     */
    public function getCityId(){
        return $this->getCurrentAccount()->getCityId();
    }

    /**
     * @return int
     */
    public function getDistrictId(){
        return $this->getCurrentAccount()->getDistrictId();
    }

    /**
     * @return int
     */
    public function getWardId(){
        return $this->getCurrentAccount()->getWardId();
    }

    /**
     * @return string
     */
    public function getIdNumber()
    {
        return $this->getCurrentAccount()->getIdNumber();
    }

    /**
     * @return string
     */
    public function getLicenseDate()
    {
        $date = $this->getCurrentAccount()->getLicenseDate();
        return $date == "0000-00-00" ? false : $date;
    }

    /**
     * @return string
     */
    public function getIssuedBy()
    {
        return $this->getCurrentAccount()->getIssuedBy();
    }

    /**
     * @return string
     */
    private function getAffiliateMediaUrl()
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . "affiliate";
        }

        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getIdFront()
    {
        $path = $this->getCurrentAccount()->getIdFront();
        if ($path) {
            return $this->getAffiliateMediaUrl() . "/" . $path;
        }
        return false;
    }

    /**
     * @return string
     */
    public function getIdBack()
    {
        $path = $this->getCurrentAccount()->getIdBack();
        if ($path) {
            return $this->getAffiliateMediaUrl() . "/" . $path;
        }
        return false;
    }
}
