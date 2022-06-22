<?php

namespace Magenest\SalesPerson\Helper;

use Magento\Framework\App\Helper\Context;

class AssignedToSales extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ADMIN_RESOURCE = 'Magenest_SalesPerson::add_queue';

    const TOPIC_NAME = 'salesperson.queue.topic';

    /**
     * @var \Magento\Framework\MessageQueue\PublisherInterface
     */
    protected $publisher;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * AssignedToSales constructor.
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\MessageQueue\PublisherInterface $publisher
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\MessageQueue\PublisherInterface $publisher,
        \Magento\Framework\Serialize\Serializer\Json $json,
        Context $context
    ) {
        $this->authSession = $authSession;
        $this->publisher = $publisher;
        $this->json = $json;
        parent::__construct($context);
    }

    public function assignQueueData($data)
    {
        try {
            $this->publisher->publish(self::TOPIC_NAME, $this->json->serialize($data));
        } catch (\Exception $exception) {
        }
    }

    public function authorizationRole($order)
    {
        return $this->authSession->getUser()->getRole()->getId() != 1 && $order->getData("assigned_to") != $this->authSession->getUser()->getId();
    }
}
