<?php

namespace Magenest\Customer\Block;

use Magenest\CustomerAvatar\Block\Attributes\Avatar;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;

/**
 * Class Account Info
 */
class AccountInfo extends Template
{
    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var Avatar
     */
    private $avatar;

    public function __construct(
        Template\Context $context,
        Session $customerSession,
        Avatar $avatar,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->avatar = $avatar;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getCustomerFullName()
    {
        try {
            if ($this->customerSession->isLoggedIn()) {
                return $this->customerSession->getCustomer()->getName();
            }
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }

    /**
     * @return string
     */
    public function getCustomerAvatarUrl()
    {
        try {
            $customerId = $this->customerSession->getCustomerId();
            return $this->avatar->getCustomerAvatarById($customerId);
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return '';
    }

    /**
     * @return string
     */
    public function getUrlCustomerEdit()
    {
        return $this->getUrl('customer/account/edit');
    }
}
