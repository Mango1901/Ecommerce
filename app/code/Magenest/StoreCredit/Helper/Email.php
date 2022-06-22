<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Helper;

use Exception;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\Core\Helper\AbstractData;

/**
 * Class Email
 * @package Magenest\StoreCredit\Helper
 */
class Email extends AbstractData
{
    const CONFIG_MODULE_PATH = 'mpstorecredit';
    const EMAIL_CONFIGURATION = '/email';

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var Account
     */
    protected $accountHelper;

    /**
     * Email constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param TransportBuilder $transportBuilder
     * @param Account $accountHelper
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        TransportBuilder $transportBuilder,
        Account $accountHelper
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->accountHelper = $accountHelper;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @param $customerId
     * @param array $templateParams
     * @param null $storeId
     * @param null $email
     *
     * @return $this
     */
    public function sendEmailTemplate(
        $customerId,
        $templateParams = [],
        $storeId = null,
        $email = null
    ) {
        $customer = $this->accountHelper->getCustomerById($customerId);
        $storeId = $storeId ?: $customer->getStoreId();

        if (!$this->isEnabledEmail($storeId) || !$customer->getMpCreditNotification()) {
            return $this;
        }

        try {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->getBalanceTemplate($storeId))
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars($templateParams)
                ->setFrom($this->getSender($storeId))
                ->addTo($email ?: $customer->getEmail(), $customer->getName())
                ->getTransport();

            $transport->sendMessage();
        } catch (Exception $e) {
            $this->_logger->critical($e->getMessage());
        }

        return $this;
    }

    /**
     * ======================================= Email Configuration ==================================================
     *
     * @param string $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigEmail($code = '', $storeId = null)
    {
        $code = ($code !== '') ? '/' . $code : '';

        return $this->getConfigValue(static::CONFIG_MODULE_PATH . self::EMAIL_CONFIGURATION . $code, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabledEmail($storeId = null)
    {
        return !!$this->getConfigEmail('enabled', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return int
     */
    public function isSubscribeByDefault($storeId = null)
    {
        return $this->getConfigEmail('subscribe_by_default', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getSender($storeId = null)
    {
        return $this->getConfigEmail('sender', $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getBalanceTemplate($storeId = null)
    {
        return $this->getConfigEmail('balance_template', $storeId);
    }
}
