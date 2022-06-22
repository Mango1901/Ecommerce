<?php
namespace Magenest\RealShippingMethod\Setup\Patch\Data;

class UpdateOrderStatus extends \Magenest\OrderManagement\Setup\Patch\Data\InstallOrderStatus
{
    const PENDING_STATUS             = 'pending';
    const WAITING_STATUS             = 'waiting';
    const ERP_SYNCED_STATUS          = 'erp_synced';
    const ERP_SYNCED_FAILED_STATUS   = 'erp_synced_failed';
    const PACKED_STATUS              = 'packed';
    const PROCESSING_SHIPMENT_STATUS = 'processing_shipment';

    /**
     * @var array[]
     */
    protected $statuses = [
        [
            'data'        => [
                'status' => self::PENDING_STATUS,
                'label'  => 'Pending'
            ],
            'state'       => 'new',
            'translation' => 'Đơn hàng mới'
        ],
        [
            'data'        => [
                'status' => self::WAITING_STATUS,
                'label'  => 'Waitng'
            ],
            'state'       => 'processing',
            'translation' => 'Chờ xử lý'
        ],
        [
            'data'        => [
                'status' => self::ERP_SYNCED_STATUS,
                'label'  => 'ERP Synced'
            ],
            'state'       => 'processing',
            'translation' => 'Đã đồng bộ'
        ],
        [
            'data'        => [
                'status' => self::ERP_SYNCED_FAILED_STATUS,
                'label'  => 'ERP Synced Failed'
            ],
            'state'       => 'processing',
            'translation' => 'Đồng bộ lỗi'
        ],
        [
            'data'        => [
                'status' => self::PACKED_STATUS,
                'label'  => 'Packed'
            ],
            'state'       => 'processing',
            'translation' => 'Đã đóng gói'
        ],
        [
            'data'        => [
                'status' => self::PROCESSING_SHIPMENT_STATUS,
                'label'  => 'Processing Shipment'
            ],
            'state'       => 'processing',
            'translation' => 'Đang giao hàng'
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function getDependencies(): array
    {
        return [\Magenest\OrderManagement\Setup\Patch\Data\InstallOrderStatus::class];
    }
}
