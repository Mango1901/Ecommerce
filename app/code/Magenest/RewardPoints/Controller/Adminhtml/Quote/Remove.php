<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Quote;

use Magento\Framework\Controller\ResultFactory;
use Magenest\RewardPoints\Model\AccountFactory;
use Magento\Backend\App\Action;


/**
 * Class Remove
 * @package Magenest\RewardPoints\Controller\Adminhtml\Quote
 */
class Remove extends Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var \Magenest\RewardPoints\Model\AccountFactory
     */
    protected $_accountFactory;

    /**
     * Remove constructor.
     * @param Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param AccountFactory $accountFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        AccountFactory $accountFactory
    )
    {
        $this->sessionQuote       = $sessionQuote;
        $this->helper            = $helper;
        $this->_customerSession   = $customerSession;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_accountFactory    = $accountFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $md5CheckSum    = $this->getRequest()->getParam('checksum');
        $md5Str         = \Magenest\RewardPoints\Block\Adminhtml\Order\RedeemPoints::$md5checksum;
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if (sha1($md5Str) == $md5CheckSum) {

            if ($this->helper->getEnableModule() && !$this->helper->getRewardTiersEnable()) {
                $this->removePointIntoQuote();
            }

        }

        return $resultRedirect;
    }

    /**
     * @throws \Exception
     */
    private function removePointIntoQuote()
    {
        $quote = $this->sessionQuote->getQuote();
        $quote->setData('reward_point', 0)->save();
        $quote->setData('reward_amount', 0)->save();
    }

}
