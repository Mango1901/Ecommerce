<?php


namespace Magenest\SocialLogin\Observer;


/**
 * Class SetSocialLoginTypeForOrder
 * @package Magenest\SocialLogin\Observer
 */
class SetSocialLoginTypeForOrder implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $socialSession;
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * SetSocialLoginTypeForOrder constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Framework\Session\SessionManagerInterface $socialSession
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Session\SessionManagerInterface $socialSession,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->socialSession = $socialSession;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        if ($this->customerSession->isLoggedIn() && $this->socialSession->getSocialType()){
            $order->setMagenestSocialType($this->socialSession->getSocialType());
            $this->orderRepository->save($order);
        }
    }
}