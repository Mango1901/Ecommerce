<?php

namespace Magenest\RewardPoints\Block\Customer;

use Magenest\RewardPoints\Helper\Data;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;
use Magenest\RewardPoints\Model\AccountFactory;

/**
 * Class Convert
 */
class Convert extends Template
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @param Template\Context $context
     * @param Data $dataHelper
     * @param Session $customerSession
     * @param AccountFactory $accountFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        Data $dataHelper,
        Session $customerSession,
        AccountFactory $accountFactory,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->_customerSession = $customerSession;
        $this->_accountFactory = $accountFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed|null
     */
    public function getRateConvert()
    {
        try {
            return $this->dataHelper->getRateConvertKpoint();
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return null;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentPoint()
    {
        try {
            $account = $this->_accountFactory->create()->load(
                $this->_customerSession->getCustomerId(),
                'customer_id'
            );

            return $account->getPointsCurrent();
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return null;
    }
}
