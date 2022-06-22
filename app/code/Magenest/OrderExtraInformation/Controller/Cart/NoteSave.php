<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Controller\Cart;

use Magento\Checkout\Controller\Cart;

/**
 * Class NoteSave
 * @package Magenest\OrderExtraInformation\Controller\Cart
 */
class NoteSave extends Cart
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $this->cart->getQuote()->setCustomerNote($this->getRequest()->getParam('customer_note'));
        $this->cart->save();

        return $this->_goBack();
    }
}
