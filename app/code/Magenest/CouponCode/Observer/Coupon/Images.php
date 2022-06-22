<?php

namespace Magenest\CouponCode\Observer\Coupon;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\App\RequestInterface;

class Images implements ObserverInterface
{
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Json
     */
    protected $serializer;
    /**
     * Data constructor
     *
     * @param RequestInterface $request
     * @param Json $serializer
     */
    public function __construct(RequestInterface $request, Json $serializer)
    {
        $this->_request = $request;
        $this->serializer = $serializer;
    }

    /**
     * Handle images
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $data = $observer->getData('rule');
        $images = $data->getData('images');
        $serializeData = $this->serializer->serialize($images);
        if ($images != null) {
            if (is_array($images)) {
                $data->setData('images', $serializeData);
            } else {
                $data->setData('images', null);
            }
            $observer->setData('rule', $data);
        }
    }
}
