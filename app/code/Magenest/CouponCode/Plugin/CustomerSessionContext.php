<?php

namespace Magenest\CouponCode\Plugin;

use Exception;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\AbstractAction;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\App\RequestInterface;
use Psr\Log\LoggerInterface;

class CustomerSessionContext
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Context
     */
    protected $httpContext;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param Session $customerSession
     * @param Context $httpContext
     * @param LoggerInterface $logger
     */
    public function __construct(
        Session         $customerSession,
        Context         $httpContext,
        LoggerInterface $logger
    ) {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->logger = $logger;
    }

    /**
     * @param AbstractAction $subject
     * @param RequestInterface $request
     * @return array
     */
    public function beforeDispatch(ActionInterface $subject, RequestInterface $request): array
    {
        try {
            $this->httpContext->setValue(
                'customer_id',
                $this->customerSession->getCustomerId(),
                false
            );

            $this->httpContext->setValue(
                'customer_group_id',
                $this->customerSession->getCustomerGroupId(),
                false
            );
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return [$request];
    }
}
