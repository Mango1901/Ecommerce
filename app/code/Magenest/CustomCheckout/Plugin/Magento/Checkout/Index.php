<?php

namespace Magenest\CustomCheckout\Plugin\Magento\Checkout;

use Magenest\CustomCheckout\Helper\ConfigHelper;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\StoreManagerInterface;

class Index
{
    /**
     * @var ManagerInterface
     */
    private $messageManger;
    /**
     * @var ConfigHelper
     */
    private $helper;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct
    (
        ManagerInterface $messageManger,
        ConfigHelper $helper,
        StoreManagerInterface $storeManager
    )
    {
        $this->messageManger = $messageManger;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }

    public function afterExecute(\Magento\Checkout\Controller\Cart\Index $subject, $result)
    {
        $storeId = $this->storeManager->getStore()->getId();
        if ($this->helper->getCheckoutEnabled($storeId)) {
            $this->messageManger->addNoticeMessage($this->helper->getCheckoutNoti($storeId));
        }
        return $result;
    }
}