<?php
/**
 * Created by PhpStorm.
 * User: thang
 * Date: 31/08/2018
 * Time: 08:23
 */

namespace Magenest\RewardPoints\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;

class Process extends Action
{
    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $sessionQuote;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * Process constructor.
     *
     * @param \Magenest\RewardPoints\Helper\Data $helper
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Action\Context $context,
        \Magenest\RewardPoints\Helper\Data $helper,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {
        $this->sessionQuote       = $sessionQuote;
        $this->helper            = $helper;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $md5CheckSum    = $this->getRequest()->getParam('checksum');
        $quote          = $this->sessionQuote->getQuote();
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $md5Str         = \Magenest\RewardPoints\Block\Adminhtml\Order\RedeemPoints::$md5checksum;
        if (sha1($md5Str) == $md5CheckSum) {
            if ($this->helper->getRewardTiersEnable() AND $this->helper->getEnableModule()) {
                if ($quote->getBaseGrandTotal() < 0) {
                    $rewardAmount = $quote->getBaseGrandTotal() + $quote->getRewardAmount();
                    $tier         = $rewardAmount / $quote->getSubtotal() * 100;
                    $quote->setData('reward_tier', $tier)->save();
                    $quote->setData('reward_amount', $rewardAmount)->save();
                    $quote->setTotalsCollectedFlag(false);
                    $quote->collectTotals()->save();
                }else{
                    $quote->setTotalsCollectedFlag(false);
                    $quote->collectTotals()->save();
                }
            }
        }
        return $resultRedirect->setData(json_encode(['type' => true]));
    }
}
