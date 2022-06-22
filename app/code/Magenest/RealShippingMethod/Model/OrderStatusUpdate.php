<?php
namespace Magenest\RealShippingMethod\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\UserFactory;
use Psr\Log\LoggerInterface;

class OrderStatusUpdate implements \Magenest\RealShippingMethod\Api\OrderStatusUpdateInterface
{
    /** @var Metadata */
    private $metadata;

    /** @var OrderFactory */
    private $orderFactory;

    /** @var UserContextInterface */
    private $userContext;

    /** @var Session */
    private $adminSession;

    /** @var UserFactory */
    private $userFactory;

    /** @var UserResource */
    private $userResource;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param Session $adminSession
     * @param Metadata $metadata
     * @param UserFactory $userFactory
     * @param OrderFactory $orderFactory
     * @param UserResource $userResource
     * @param LoggerInterface $logger
     * @param UserContextInterface $userContext
     */
    public function __construct(
        Session              $adminSession,
        Metadata             $metadata,
        UserFactory          $userFactory,
        OrderFactory         $orderFactory,
        UserResource         $userResource,
        LoggerInterface      $logger,
        UserContextInterface $userContext
    ) {
        $this->logger       = $logger;
        $this->metadata     = $metadata;
        $this->userFactory  = $userFactory;
        $this->userContext  = $userContext;
        $this->userResource = $userResource;
        $this->orderFactory = $orderFactory;
        $this->adminSession = $adminSession;
    }

    /**
     * @inheritDoc
     */
    public function save(array $entities): array
    {
        $result = [];

        if ($this->adminSession->getUser() === null) {
            $this->setAdminUser();
        }

        foreach ($entities as $entity) {
            try {
                $order = $this->orderFactory->create()->loadByIncrementId($entity->getIncrementId());
                if ($order->getId() && $entity->getState() && $entity->getStatus()) {
                    $order->addData([
                        'state'            => $entity->getState(),
                        'status'           => $entity->getStatus(),
                        'authorized_admin' => $this->adminSession->getUser()->getId()
                    ]);

                    $this->metadata->getMapper()->save($order);
                    $result[] = $order;
                }
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }

        return $result;
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    private function setAdminUser()
    {
        if ($this->userContext->getUserType() == UserContextInterface::USER_TYPE_INTEGRATION) {
            $adminUser = $this->userFactory->create();
            $this->userResource->load($adminUser, $this->userContext->getUserId());
            $this->adminSession->setUser($adminUser);
        } else {
            throw new LocalizedException(__('Only integration users can access this endpoint.'));
        }
    }
}
