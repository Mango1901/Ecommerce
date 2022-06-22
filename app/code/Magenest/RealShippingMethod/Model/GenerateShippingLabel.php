<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 * Created by PhpStorm.
 * User: crist
 * Date: 10/06/2021
 * Time: 10:51
 */

namespace Magenest\RealShippingMethod\Model;

use Magenest\API247\Model\API247Post;
use Magenest\API247\Model\Carrier\API247;
use Magenest\GiaoHangTietKiem\Model\Carrier\GiaoHangTietKiem;
use Magenest\ViettelPostCarrier\Model\Carrier\ViettelPost;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Shipping\LabelGenerator;

class GenerateShippingLabel
{
    const ALLOWED_CARRIERS = [
        GiaoHangTietKiem::CODE,
        ViettelPost::CODE,
        API247::CODE
    ];

    /** @var LabelGenerator */
    protected $labelGenerator;

    /** @var RequestInterface */
    private $request;

    public function __construct(
        LabelGenerator   $labelGenerator,
        RequestInterface $request
    ) {
        $this->labelGenerator = $labelGenerator;
        $this->request        = $request;
    }

    /**
     * @param Order $order
     *
     * @throws LocalizedException
     */
    public function generateShippingLabel($order)
    {
        $shipments = $order->getShipmentsCollection();
        /** @var Order\Shipment $shipment */
        foreach ($shipments as $shipment) {
            $weight       = 0;
            $customValue  = 0;
            $packageItems = [];
            $shipment->getOrder()->setData('shipping_method', $order->getShippingMethod());
            foreach ($shipment->getAllItems() as $item) {
                if ($item->getProductType() == Configurable::TYPE_CODE
                    || $item->getProductType() == Type::TYPE_VIRTUAL) {
                    continue;
                }
                $weight += $item->getWeight();
                if ($item->getParentItem()) {
                    $price = $item->getParentItem()->getPrice();
                } else {
                    $price = $item->getPrice();
                }
                $customValue                      += $price;
                $packageItems[$item->getItemId()] = [
                    'qty'           => $item->getQtyOrdered(),
                    'custom_value'  => $price,
                    'price'         => $price,
                    'name'          => $item->getName(),
                    'weight'        => '',
                    'product_id'    => $item->getProductId(),
                    'order_item_id' => $item->getItemId()
                ];
            }
            $packages = [
                1 => [
                    'params' => [
                        'weight_units'       => 'KILOGRAM',
                        'dimension_units'    => 'CENTIMETER',
                        'custom_value'       => $customValue,
                        'weight'             => $weight,
                        'container'          => '',
                        'length'             => '',
                        'width'              => '',
                        'height'             => '',
                        'content_type'       => '',
                        'content_type_other' => ''
                    ],
                    'items'  => $packageItems
                ]
            ];
            $request  = clone $this->request;
            $request->setParams(['packages' => $packages]);
            $this->labelGenerator->create($shipment, $request);
        }
    }
}
