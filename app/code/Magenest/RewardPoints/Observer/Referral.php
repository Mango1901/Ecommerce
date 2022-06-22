<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ReferAFriend extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Observer;

use Magenest\Affiliate\Helper\Data as AffiliateHelper;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\AffiliateCatalogRule\Helper\Constant;
use Magenest\RewardPoints\Api\IsEarnedPointFromClickInterface;
use Magenest\RewardPoints\Cookie\ReferralCode;
use Magenest\RewardPoints\Helper\Data;
use Magenest\RewardPoints\Model\RefereeClickTrack;
use Magenest\RewardPoints\Model\RefereeClickTrackFactory;
use Magenest\RewardPoints\Model\ReferralFactory;
use Magenest\RewardPoints\Model\ResourceModel\RefereeClickTrack as RefereeClickTrackResourceModel;
use Magenest\RewardPoints\Model\Rule;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Stdlib\Cookie\CookieSizeLimitReachedException;
use Magento\Framework\Stdlib\Cookie\FailureToSendException;
use Magento\Framework\UrlInterface;

class Referral implements ObserverInterface
{
    const COOKIE_NAME = 'referralcode';

    /**
     * @var ReferralCode
     */
    protected $referralCodeCookie;

    /**
     * @var Data
     */
    protected $referHelper;

    /**
     * @var UrlInterface
     */
    protected $url;

    /**
     * System event manager
     *
     *
     * @var ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var ReferralFactory
     */
    protected $referralFactory;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var AccountFactory
     */
    protected $accountFactory;

    /**
     * @var AffiliateHelper
     */
    protected $_affiliateHelper;

    /**
     * @var IsEarnedPointFromClickInterface
     */
    protected $isEarnedPointFromClick;

    /**
     * @var RefereeClickTrackFactory
     */
    protected $refereeClickTrackFactory;

    /**
     * @var RefereeClickTrackResourceModel
     */
    protected $refereeClickTrackResourceModel;

    /**
     * Referral constructor.
     * @param ReferralCode $referralCodeCookie
     * @param Data $referHelper
     * @param UrlInterface $url
     * @param ManagerInterface $eventManager
     * @param ReferralFactory $referralFactory
     * @param Session $customerSession
     * @param Context $httpContext
     * @param AccountFactory $accountFactory
     * @param AffiliateHelper $affiliateHelper
     * @param IsEarnedPointFromClickInterface $isEarnedPointFromClick
     * @param RefereeClickTrackFactory $refereeClickTrackFactory
     * @param RefereeClickTrackResourceModel $refereeClickTrackResourceModel
     */
    public function __construct(
        ReferralCode                    $referralCodeCookie,
        Data                            $referHelper,
        UrlInterface                    $url,
        ManagerInterface                $eventManager,
        ReferralFactory                 $referralFactory,
        Session                         $customerSession,
        Context                         $httpContext,
        AccountFactory                  $accountFactory,
        AffiliateHelper                 $affiliateHelper,
        IsEarnedPointFromClickInterface $isEarnedPointFromClick,
        RefereeClickTrackFactory        $refereeClickTrackFactory,
        RefereeClickTrackResourceModel  $refereeClickTrackResourceModel
    ) {
        $this->referralCodeCookie = $referralCodeCookie;
        $this->referHelper = $referHelper;
        $this->url = $url;
        $this->_eventManager = $eventManager;
        $this->referralFactory = $referralFactory;
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->accountFactory = $accountFactory;
        $this->_affiliateHelper = $affiliateHelper;
        $this->isEarnedPointFromClick = $isEarnedPointFromClick;
        $this->refereeClickTrackFactory = $refereeClickTrackFactory;
        $this->refereeClickTrackResourceModel = $refereeClickTrackResourceModel;
    }

    /**
     * @param Observer $observer
     * @throws InputException
     * @throws CookieSizeLimitReachedException
     * @throws FailureToSendException
     */
    public function execute(Observer $observer)
    {
        if (!$this->referHelper->isReferByLinkEnabled()) {
            return;
        }

        $referralCode = $this->getReferralCodeFromRequest($observer->getEvent()->getRequest());

        if ($referralCode === null) {
            return;
        }

        $customerCode = $this->getReferralCodeOfAccount();
        if ($referralCode === $customerCode) {
            return;
        }

        $referUrl = $this->_affiliateHelper->getDefaultReferLink();
        $currentUrl = $this->url->getCurrentUrl();

        if (strpos($referUrl, $currentUrl) === false) {
            return;
        }

        $this->referralCodeCookie->delete();
        $this->referralCodeCookie->set($referralCode);
        $this->earnPointWhenRefereeClick($referralCode);
    }

    /**
     * @param $request
     * @return string
     */
    private function getReferralCodeFromRequest($request)
    {
        $referralCode = $request->getParam(self::COOKIE_NAME);
        if (!$referralCode) {
            $referralCode = $this->referralCodeCookie->get();
        }

        return $referralCode;
    }

    /**
     * @return mixed|string
     */
    private function getReferralCodeOfAccount()
    {
        $affiliateContext = $this->httpContext->getValue(Constant::IS_AFFILIATE_CONTEXT);
        if ($affiliateContext) {
            $customerId = $this->customerSession->getId();
            $affiliateCustomer = $this->accountFactory->create()->load($customerId, 'customer_id');
            if ($affiliateCustomer && $affiliateCustomer->getId()) {
                return $affiliateCustomer->getCode();
            }
        }

        return $this->referHelper->getReferralCode();
    }

    /**
     *
     */
    private function earnPointWhenRefereeClick($code)
    {
        $customerId = $this->customerSession->getCustomerId();
        $applyCustomerId = $this->getRefereeAccountId($code);

        if (!$this->isEarnedPointFromClick->execute($customerId, $applyCustomerId)) {
            $dataArr = [
                'customer_id' => $customerId,
                'apply_customer_id' => $this->getRefereeAccountId($code),
                'referral_earning_type' => $this->referHelper->getReferralEarningType(),
                'condition_type' => Rule::CONDITION_EARN_WHEN_REFEREE_CLICKED
            ];

            $refereeClickTrack = $this->refereeClickTrackFactory->create()->setData($dataArr);
            $this->refereeClickTrackResourceModel->save($refereeClickTrack);

            $applyObj = new DataObject($dataArr);
            // apply referral code
            $this->_eventManager->dispatch('apply_referral_code', ['applyObj' => $applyObj]);
        }
    }

    /**
     * @return mixed|string
     */
    private function getRefereeAccountId($code)
    {
        $refereeAffiliateCustomer = $this->accountFactory->create()->load($code, 'code');
        if ($refereeAffiliateCustomer && $refereeAffiliateCustomer->getId()) {
            return $refereeAffiliateCustomer->getCustomerId();
        }

        return $this->referralFactory->create()->load($code, 'referral_code')->getData('customer_id');
    }
}
