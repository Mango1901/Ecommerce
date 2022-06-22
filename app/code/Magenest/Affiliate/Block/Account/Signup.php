<?php


namespace Magenest\Affiliate\Block\Account;

use Magenest\Affiliate\Block\Account;
use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magenest\Affiliate\Helper\Payment;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\Affiliate\Model\CampaignFactory;
use Magenest\Affiliate\Model\TransactionFactory;
use Magenest\Affiliate\Model\WithdrawFactory;
use Magento\Customer\Block\Address\Edit;
use Magento\Customer\Helper\View;
use Magento\Customer\Model\Session;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Signup
 * @package Magenest\Affiliate\Block\Account
 */
class Signup extends Account
{
    /**
     * @var Edit
     */
    protected $address;

    /**
     * @param Edit $address
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
     * @param array $data
     */
    public function __construct(
        Edit $address,
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
        array $data = []
    )
    {
        $this->address = $address;
        parent::__construct($context, $customerSession, $helperView, $affiliateHelper, $paymentHelper, $jsonHelper, $registry, $pricingHelper, $objectManager, $campaignFactory, $accountFactory, $withdrawFactory, $transactionFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Signup Affiliate'));

        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getSignUpUrl()
    {
        return $this->getUrl('affiliate/account/signuppost');
    }

    /**
     * @return string
     */
    public function getBackButtonUrl()
    {
        return $this->getUrl('affiliate/');
    }

    public function callAddress(){
        return $this->address;
    }

    /**
     * Return the associated address.
     *
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function getAddress()
    {
        return $this->_address;
    }
}
