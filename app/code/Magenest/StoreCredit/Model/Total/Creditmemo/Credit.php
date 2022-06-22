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

namespace Magenest\StoreCredit\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magenest\StoreCredit\Helper\Calculation as Helper;

/**
 * Class Credit
 * @package Magenest\StoreCredit\Model\Total\Creditmemo
 */
class Credit extends AbstractTotal
{
    /**
     * @var Helper
     */
    protected $helper;

    /**
     * Credit constructor.
     *
     * @param Helper $helper
     * @param array $data
     */
    public function __construct(
        Helper $helper,
        array $data = []
    ) {
        $this->helper = $helper;

        parent::__construct($data);
    }

    /**
     * @param Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $baseDiscount = $creditmemo->getBaseSubtotal();
        $discount = $creditmemo->getSubtotal();

        $order = $creditmemo->getOrder();

        $extraContent = Helper::jsonDecode($order->getMpStoreCreditExtraContent());

        if (!empty($extraContent['apply_for_shipping'])) {
            $baseDiscount += $creditmemo->getBaseShippingAmount();
            $discount += $creditmemo->getShippingAmount();
        }

        if (!empty($extraContent['apply_for_tax'])) {
            $baseDiscount += $creditmemo->getBaseTaxAmount();
            $discount += $creditmemo->getTaxAmount();
        }

        $applyForProduct = isset($extraContent['apply_for_product']) ? $extraContent['apply_for_product'] : [];
        /** @var Creditmemo\Item $item */
        foreach ($creditmemo->getAllItems() as $item) {
            $orderItem = $item->getOrderItem();

            if ($orderItem->getHasChildren() && $orderItem->isChildrenCalculated()) {
                continue;
            }

            $type = $orderItem->getParentItem() ? $orderItem->getParentItem()->getProductType() : $orderItem->getProductType();

            if (!in_array($type, $applyForProduct)) {
                $baseDiscount -= $item->getBaseRowTotal();
                $discount -= $item->getRowTotal();
            }
        }

        $baseTotalDiscount = min(
            $baseDiscount,
            $order->getMpStoreCreditBaseDiscount() - $this->helper->getAppliedBaseDiscount($creditmemo)
        );
        $totalDiscount = min(
            $discount,
            $order->getMpStoreCreditDiscount() - $this->helper->getAppliedDiscount($creditmemo)
        );

        $creditmemo->setMpStoreCreditBaseDiscount($baseTotalDiscount);
        $creditmemo->setMpStoreCreditDiscount($totalDiscount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscount);
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscount);

        if ($creditmemo->getGrandTotal() == 0 && $totalDiscount > 0) {
            $creditmemo->setAllowZeroGrandTotal(true);
        }

        return $this;
    }
}
