<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magenest\RewardPoints\Model\AccountFactory;


/**
 * Class Add
 * @package Magenest\RewardPoints\Controller\Adminhtml\Quote
 */
class Add extends \Magento\Backend\App\Action
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
     * Add constructor.
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
    ) {
        $this->sessionQuote       = $sessionQuote;
        $this->_customerSession   = $customerSession;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_accountFactory    = $accountFactory;
        $this->helper            = $helper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $point          = $this->getRequest()->getParam('redeemPoints');
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        if ($this->helper->getEnableModule() && !$this->helper->getRewardTiersEnable()) {
            $this->addPointIntoQuote($point);
        }
        $urlLoadBlockFirst= $this->_url->getUrl('sales/order_create/loadBlock',['block'=>'totals','json'=>true]);
        $urlLoadBlockSecond = $this->_url->getUrl('sales/order_create/loadBlock',['block'=>'card_validation']);
        $resultRedirect->setData(['url_load_block_first'=>$urlLoadBlockFirst, 'url_load_block_second'=>$urlLoadBlockSecond]);
        return $resultRedirect;
    }

    /**
     * @param $points
     *
     * @throws \Exception
     */
    private function addPointIntoQuote($points)
    {
        $quote = $this->sessionQuote->getQuote();

        if ($this->isValidToAddToCart($quote, $points)) {
            $quote->setData('reward_point', $points)->save();
            $rewardDiscountAmt = min($this->getRewardDiscountAmount($points), $quote->getBaseGrandTotal());
            $quote->setData('reward_amount', $rewardDiscountAmt)->save();
            $a  = $quote->getTotals();

        }
    }

    /**
     * @param $point
     *
     * @return mixed
     */
    public function getRewardDiscountAmount($point)
    {
        return $this->helper->getRewardBaseAmount($point);
    }

    /**
     * @param $quote
     * @param $point
     *
     * @return bool
     */
    private function isValidToAddToCart($quote, $point)
    {
        $customerId   = $this->sessionQuote->getCustomerId();
        $currentPoint = $this->getAccount($customerId)->getPointsCurrent();
        if ($currentPoint < $point || $point < 0 || $this->helper->getRewardBaseAmount($point) > ceil($quote->getBaseGrandTotal())) {
            return false;
        }

        return true;
    }

    /**
     * @param $customerId
     *
     * @return \Magenest\RewardPoints\Model\Account
     */
    public function getAccount($customerId)
    {
        $account = $this->_accountFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customerId)
            ->getFirstItem();

        return $account;
    }

}
