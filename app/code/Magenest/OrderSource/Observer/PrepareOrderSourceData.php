<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 17/02/2021
 * Time: 08:32
 */

namespace Magenest\OrderSource\Observer;


use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class PrepareOrderSourceData
 * @package Magenest\OrderSource\Observer
 */
class PrepareOrderSourceData implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * PrepareOrderSourceData constructor.
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Prepare order source data
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getOrder();
        $orderSource = $this->request->getParam('order_source');
        $order->setData('order_source', $orderSource);
    }
}
