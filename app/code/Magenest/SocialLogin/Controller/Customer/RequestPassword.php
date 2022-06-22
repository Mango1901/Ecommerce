<?php


namespace Magenest\SocialLogin\Controller\Customer;


use Magento\Framework\App\Action\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class RequestPassword
 * @package Magenest\SocialLogin\Controller\Customer
 */
class RequestPassword extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $redirectResult;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $socialSession;

    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory
     */
    protected $socialAccountCollection;

    /**
     * RequestPassword constructor.
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection
     * @param \Magento\Framework\Session\SessionManagerInterface $socialSession
     * @param \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectResult
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection,
        \Magento\Framework\Session\SessionManagerInterface $socialSession,
        \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectResult,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Action\Context $context)
    {
        parent::__construct($context);
        $this->redirectResult = $redirectResult;
        $this->customerSession = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->socialSession = $socialSession;
        $this->socialAccountCollection = $socialAccountCollection;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectResult = $this->redirectResult->create();
        $customer = $this->customerSession->getCustomer();
        $socialAccount = $this->socialAccountCollection->create()
                                                                 ->addFieldToFilter('customer_id',['eq'=>$customer->getId()])
                                                                 ->addFieldToFilter('social_email',['eq'=>$customer->getEmail()])
                                                                 ->getFirstItem();
        $email = $customer->getEmail();
        try {
            if (!$email || !$socialAccount->getExistEmail()) {
                throw new LocalizedException(__('We\'re unable to send the password reset email. Please contact with admin to reset your password.'));
            }
            $this->customerAccountManagement->initiatePasswordReset(
                $email,
                \Magento\Customer\Model\AccountManagement::EMAIL_RESET
            );
        } catch (\Exception $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('We\'re unable to send the password reset email. Please contact with admin to reset your password.')
            );
            return $redirectResult->setPath('*/*/account');
        }
        $this->messageManager->addSuccessMessage(__('Reset password email has been sent, You can check your email.'));
        return $redirectResult->setPath('*/*/account');
    }
}