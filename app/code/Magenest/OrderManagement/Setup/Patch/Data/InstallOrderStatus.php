<?php
namespace Magenest\OrderManagement\Setup\Patch\Data;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class InstallOrderStatus implements DataPatchInterface
{
    /**
     * @var array[]
     */
    protected $statuses = [
        [
            'data'        => [
                'status' => 'preorder',
                'label'  => 'Pre-Order'
            ],
            'state'       => 'new',
            'translation' => 'Đặt hàng trước'
        ],
        [
            'data'        => [
                'status' => 'pending',
                'label'  => 'Pending'
            ],
            'state'       => 'new',
            'translation' => 'Chờ xử lý'
        ],
        [
            'data'        => [
                'status' => 'confirmed',
                'label'  => 'Confirmed'
            ],
            'state'       => 'processing',
            'translation' => 'Đã xác nhận'
        ],
        [
            'data'        => [
                'status' => 'processing_shipment',
                'label'  => 'Processing Shipment'
            ],
            'state'       => 'processing',
            'translation' => 'Đã giao hàng cho bên vận chuyển'
        ],
        [
            'data'        => [
                'status' => 'complete_shipment',
                'label'  => 'Delivered'
            ],
            'state'       => 'complete',
            'translation' => 'Đã giao hàng'
        ],
        [
            'data'        => [
                'status' => 'paid',
                'label'  => 'Paid'
            ],
            'state'       => 'processing',
            'translation' => 'Đã thanh toán'
        ],
        [
            'data'        => [
                'status' => 'complete',
                'label'  => 'Complete'
            ],
            'state'       => 'complete',
            'translation' => 'Đã Hoàn thành'
        ],
        [
            'data'        => [
                'status' => 'canceled',
                'label'  => 'Canceled'
            ],
            'state'       => 'canceled',
            'translation' => 'Đã hủy'
        ],
        [
            'data'        => [
                'status' => 'pending_exchange',
                'label'  => 'Pending Exchange'
            ],
            'state'       => 'new',
            'translation' => 'Yêu cầu đổi'
        ],
        [
            'data'        => [
                'status' => 'confirmed_exchange',
                'label'  => 'Confirmed Exchange'
            ],
            'state'       => 'processing',
            'translation' => 'Đã xác nhận đổi'
        ],
        [
            'data'        => [
                'status' => 'completed_exchange',
                'label'  => 'Complete Exchange'
            ],
            'state'       => 'complete',
            'translation' => 'Đã đổi hàng'
        ]
    ];

    /** @var array */
    private $vietnameseStores = [];

    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var StatusFactory */
    private $statusFactory;

    /** @var Status */
    private $statusResource;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param StatusFactory $statusFactory
     * @param Status $statusResource
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        StoreManagerInterface    $storeManager,
        ScopeConfigInterface     $scopeConfig,
        StatusFactory            $statusFactory,
        Status                   $statusResource
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->statusFactory = $statusFactory;
        $this->statusResource = $statusResource;
        $this->getVietnameseStores();
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();

        $statusModel = $this->statusFactory->create();
        foreach ($this->statuses as $status) {
            $statusModel->unsetData();
            $this->statusResource->load($statusModel, $status['data']['status']);

            if (!empty($this->vietnameseStores)) {
                $status['data']['store_labels'] = array_fill_keys($this->vietnameseStores, $status['translation']);
            }

            $statusModel->setData($status['data']);
            $this->statusResource->save($statusModel);
            $statusModel->assignState($status['state'], false, true);
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * @return void
     */
    public function getVietnameseStores()
    {
        $stores = $this->storeManager->getStores(false);
        foreach ($stores as $id => $store) {
            $locale = $this->scopeConfig->getValue(Data::XML_PATH_DEFAULT_LOCALE, ScopeInterface::SCOPE_STORE, $id);
            if ($locale === 'vi_VN') {
                $this->vietnameseStores[] = $id;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
