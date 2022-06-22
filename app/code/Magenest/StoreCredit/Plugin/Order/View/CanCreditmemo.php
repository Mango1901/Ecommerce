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

namespace Magenest\StoreCredit\Plugin\Order\View;

use Magento\Sales\Model\Order;
use Magenest\StoreCredit\Helper\Calculation;

/**
 * Class CanCreditmemo
 * @package Magenest\StoreCredit\Plugin\Order\View
 */
class CanCreditmemo
{
    /**
     * @var Calculation
     */
    private $helper;

    /**
     * CanCreditmemo constructor.
     *
     * @param Calculation $helper
     */
    public function __construct(Calculation $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param Order $subject
     */
    public function beforeCanCreditmemo(Order $subject)
    {
        if (in_array($subject->getState(), [
                Order::STATE_PROCESSING,
                Order::STATE_COMPLETE,
                Order::STATE_CLOSED
            ]) && $subject->getMpStoreCreditDiscount()) {
            $value = $this->validateQty($subject) && $this->validateCredit($subject);

            $subject->setForcedCanCreditmemo($value);
        }
    }

    /**
     * @param Order $subject
     *
     * @return bool
     */
    public function validateCredit($subject)
    {
        $credit = $subject->getMpStoreCreditDiscount();

        foreach ($subject->getCreditmemosCollection() as $creditmemo) {
            $credit -= $creditmemo->getMpStoreCreditDiscount();
        }

        return floatval($credit) > 0 || $this->helper->isAllowRefundExchange($subject->getStoreId());
    }

    /**
     * @param Order $subject
     *
     * @return bool
     */
    public function validateQty($subject)
    {
        foreach ($subject->getItems() as $item) {
            if ($item->getQtyRefunded() < $item->getQtyOrdered()) {
                return true;
            }
        }

        return false;
    }
}
