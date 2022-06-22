<?php
/**
 * Created by Magenest JSC.
 * Time: 9:41
 */

namespace Magenest\SocialLogin\Controller\Customer;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

/**
 * Class Unlink
 * @package Magenest\SocialLogin\Controller\Customer
 */
class Unlink extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount
     */
    protected $socialAccountResource;
    /**
     * @var \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory
     */
    protected $socialAccountCollection;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $socialSession;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Unlink constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Magento\Framework\Session\SessionManagerInterface $socialSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource
     * @param \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection
     * @param \Magento\Framework\App\Action\Context $context
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Framework\Session\SessionManagerInterface $socialSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount $socialAccountResource,
        \Magenest\SocialLogin\Model\ResourceModel\SocialAccount\CollectionFactory $socialAccountCollection,
        Context $context
    ) {
        parent::__construct($context);
        $this->customerSession         = $customerSession;
        $this->socialAccountResource   = $socialAccountResource;
        $this->socialAccountCollection = $socialAccountCollection;
        $this->socialSession           = $socialSession;
        $this->orderCollection         = $orderCollection;
        $this->orderRepository         = $orderRepository;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        $socialAccountCollection = $this->socialAccountCollection->create()
                                                                 ->addFieldToFilter('customer_id',$this->customerSession->getId())
                                                                 ->addFieldToFilter('social_login_type',$type);
        try {
            $this->socialAccountResource->delete($socialAccountCollection->getFirstItem());
            $orderCollection = $this->orderCollection->create()
                                                     ->addAttributeToFilter('customer_id',$this->customerSession->getId())
                                                     ->addAttributeToFilter('magenest_social_type',$type);
            foreach ($orderCollection as $order) {
                $order->setMagenestSocialType(null);
                $this->orderRepository->save($order);
            }
            if ($type ==  $this->socialSession->getSocialType()) {
                $socialAccount = $this->socialAccountCollection->create()
                                                               ->addFieldToFilter('customer_id',$this->customerSession->getId())
                                                               ->getFirstItem();
                $this->socialSession->setSocialType($socialAccount->getSocialLoginType());
            }
            $this->messageManager->addSuccessMessage(__('You have been unlinked your %1 account successfully!',$type));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e, __('An error occurred while unlink)'));
        }
        $resultPage = $this->resultRedirectFactory->create();
        $resultPage->setUrl($this->_redirect->getRefererUrl());
        return $resultPage;
    }
}
