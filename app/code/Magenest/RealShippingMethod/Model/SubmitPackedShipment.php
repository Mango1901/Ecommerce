<?php
namespace Magenest\RealShippingMethod\Model;

use Magenest\RealShippingMethod\Setup\Patch\Data\UpdateOrderStatus;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\User\Model\ResourceModel\User as UserResource;
use Magento\User\Model\UserFactory;
use Psr\Log\LoggerInterface;

class SubmitPackedShipment
{
    /** @var OrderRepositoryInterface */
    private $orderRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var GenerateShippingLabel */
    private $labelGenerator;

    /** @var LoggerInterface */
    private $logger;

    /** @var Session */
    private $adminSession;

    /** @var UserFactory */
    private $userFactory;

    /** @var UserResource */
    private $userResource;

    /**
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GenerateShippingLabel $labelGenerator
     * @param LoggerInterface $logger
     * @param Session $adminSession
     * @param UserFactory $userFactory
     * @param UserResource $userResource
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder    $searchCriteriaBuilder,
        GenerateShippingLabel    $labelGenerator,
        LoggerInterface          $logger,
        Session                  $adminSession,
        UserFactory              $userFactory,
        UserResource             $userResource
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository       = $orderRepository;
        $this->labelGenerator        = $labelGenerator;
        $this->adminSession          = $adminSession;
        $this->userFactory           = $userFactory;
        $this->userResource          = $userResource;
        $this->logger                = $logger;
    }

    /**
     * @return void
     */
    public function execute()
    {
        return ;
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('status', UpdateOrderStatus::PACKED_STATUS)
            ->addFilter('real_shipping_method', GenerateShippingLabel::ALLOWED_CARRIERS, 'in')
            ->addFilter('api_order_id', true, 'null')
            ->create();
        $orders         = $this->orderRepository->getList($searchCriteria)->getItems();
        /** @var Order $order */
        foreach ($orders as $order) {
            try {
                if ($this->adminSession->getUser() === null) {
                    $this->setAdminUser($order);
                }

                $order->setData(
                    'shipping_method',
                    $order->getRealShippingMethod() . "_" . $order->getRealShippingMethod()
                );
                $this->labelGenerator->generateShippingLabel($order);
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage(), ['trace' => $e->getTraceAsString()]);
            }
        }
    }

    /**
     * @param Order $order
     *
     * @return void
     * @throws LocalizedException
     */
    private function setAdminUser($order)
    {
        $adminUser = $this->userFactory->create();
        $this->userResource->load($adminUser, $order->getAuthorizedAdmin());
        if ($adminUser->getId()) {
            $this->adminSession->setUser($adminUser);
        } else {
            throw new LocalizedException(
                __(
                    "Can't proceed with unauthorized user. Please submit shipment for order ID %1 manually.",
                    $order->getIncrementId()
                )
            );
        }
    }
}
