<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Reward points extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 09/11/2020 15:22
 */

namespace Magenest\RewardPoints\Controller\Customer;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\Serializer\Json;

class DisableNotifications extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Session
     */
    protected $_customerSession;

    protected $_json;

    /**
     * DisableNotifications constructor.
     * @param Session $customerSession
     * @param Context $context
     */
    public function __construct(
        Json $json,
        Session $customerSession,
        Context $context
    ) {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->_json = $json;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $notificationsDisabled = empty($this->_customerSession->getNotificationsDisabled()) ? [] : $this->_json->unserialize($this->_customerSession->getNotificationsDisabled());
            if ($this->getRequest()->getParam('notification_id')) {
                $notificationsDisabled[] = $this->getRequest()->getParam('notification_id');
            }
            $this->_customerSession->setNotificationsDisabled($this->_json->serialize($notificationsDisabled));

            $this->messageManager->addSuccess(__('The notification has been disable'));
        } catch (\Exception $exception) {
            $this->messageManager->addError($exception->getMessage());
        }
        return $resultRedirect->setPath($this->_redirect->getRefererUrl());
    }
}