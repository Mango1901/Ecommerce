<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 * Magenest_ReferAFriend extension
 * NOTICE OF LICENSE
 * @category Magenest
 * @package  Magenest_ReferAFriend
 */

namespace Magenest\RewardPoints\Controller\Referral;

use Magento\Framework\Controller\ResultFactory;

class SendEmail extends \Magento\Framework\App\Action\Action
{
    const XML_PATH_EMAIL_TEMPLATE_ID = 'rewardpoints/referafriend/referral_setting/refer_via_email';

    /**
     * @var \Magenest\RewardPoints\Helper\Email
     */
    protected $emailHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magenest\RewardPoints\Block\Customer\Friend
     */
    protected $pointsBlock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory */
    protected $myReferralFactory;

    /**
     * SendEmail constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magenest\RewardPoints\Helper\Email $emailHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magenest\RewardPoints\Block\Customer\Points $pointsBlock
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magenest\RewardPoints\Helper\Email $emailHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magenest\RewardPoints\Block\Customer\Friend $pointsBlock,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magenest\RewardPoints\Model\MyReferralFactory $myReferralFactory
    ) {
        $this->emailHelper = $emailHelper;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->url = $context->getUrl();
        $this->pointsBlock = $pointsBlock;
        $this->scopeConfig = $scopeConfig;
        $this->myReferralFactory = $myReferralFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->customerSession->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(__('Invalid request.'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());

            return $resultRedirect;
        }

        try {
            $data = $this->getRequest()->getPostValue();

            $customerEmail = $this->customerSession->getCustomer()->getEmail();
            $emailList = $data['recipients']["email"];
            if (in_array($customerEmail, $emailList)) {
                $this->messageManager->addErrorMessage(__('You can\'t invite yourself.'));
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());

                return $resultRedirect;
            }

            $templateId = $this->getEmailTemplateId();

            $referralLink = $this->pointsBlock->getReferralLink();
            $customer = $this->customerSession->getCustomer();
            if ($customer->getMiddlename() === null) {
                $senderName = $customer->getFirstname() . ' ' . $customer->getLastname();
            } else {
                $senderName = $customer->getFirstname() . ' ' . $customer->getMiddlename() . ' ' . $customer->getLastname();
            }

            $sender = $this->emailHelper->getSender();
            foreach ($data['recipients']["name"] as $key => $value) {
                $templateVars = [
                    'name' => $value,
                    'sender_name' => $senderName,
                    'message' => $data['sender']["message"],
                    'referral_url' => $referralLink,
                ];

                $recipient = [
                    'name' => $value,
                    'email' => $data['recipients']["email"][$key],
                ];
                $myReferralData = [
                    'email_referred' => $data['recipients']["email"][$key],
                    'customer_id' => $customer->getId(),
                    'status' => '0'
                ];
                $this->saveMyReferral($myReferralData);
                $this->emailHelper->sendCustomEmail($templateId, $templateVars, $sender, $recipient);
            }
            $this->messageManager->addSuccessMessage(__('Message has been sent successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Something is wrong.'));
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());

        return $resultRedirect;
    }

    /**
     * Get template id for send email
     * @return mixed
     */
    public function getEmailTemplateId()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_TEMPLATE_ID,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function saveMyReferral($data)
    {
        $myReferral = $this->myReferralFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $data['customer_id'])
            ->addFieldToFilter('email_referred', $data['email_referred'])
            ->getFirstItem();
        if ($myReferral->getId()) {
            $now = new \DateTime();
            $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);
            $myReferral->setUpdatedAt($now)->save();
        } else {
            $myReferral->setData($data)->save();
        }
    }
}