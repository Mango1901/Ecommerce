<?php


namespace Magenest\NotificationBox\Setup\Patch\Data;

use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\Notification;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

class AddPointUpdateNotificationType implements DataPatchInterface
{
    /** @var string Notification icon directory url */
    const URL_ICON = 'notificationtype/icon';

    /** @var string default icon directory url */
    const URL_ICON_DEFAULT = '/view/adminhtml/web/images/defaultImage';

    /** @var Helper  */
    private $helper;

    /** @var StoreManagerInterface  */
    private $storeManagerInterface;

    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $path;
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Helper $helper
     * @param StoreManagerInterface $storeManagerInterface
     * @param DirectoryList $dir
     * @param ComponentRegistrarInterface $path
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        Helper $helper,
        StoreManagerInterface $storeManagerInterface,
        DirectoryList $dir,
        ComponentRegistrarInterface $path
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->storeManagerInterface = $storeManagerInterface;
        $this->helper = $helper;
        $this->dir = $dir;
        $this->path = $path;
    }

    /**
     * @inheirtDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $currentStore = $this->storeManagerInterface->getStore();

        $mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . self::URL_ICON;

        $listDefaultImage = $this->helper->getDefaultImage();

        $data = [
            [
                'name' => Notification::REWARD_POINT_REMINDS_LABEL,
                'description' => Notification::REWARD_POINT_REMINDS_LABEL,
                'is_category' => 1,
                'default_type' => Notification::REWARD_POINT_REMINDS,
                'icon' => '[{
                                "name": "'.Notification::ABANDONED_CART_REMINDS.'",
                                "type": "image/png",
                                "url": "'.$mediaUrl.$listDefaultImage[Notification::ABANDONED_CART_REMINDS].'",
                                "size":"1093"
                    }]'
            ],
            [
                'name' => Notification::STORE_CREDIT_REMINDS_LABEL,
                'description' => Notification::STORE_CREDIT_REMINDS_LABEL,
                'is_category' => 1,
                'default_type' => Notification::STORE_CREDIT_REMINDS,
                'icon' => '[{
                                "name": "'.Notification::ORDER_STATUS_UPDATE.'",
                                "type": "image/png",
                                "url": "'.$mediaUrl.$listDefaultImage[Notification::ORDER_STATUS_UPDATE].'",
                                "size":"1474"
                    }]'
            ]
        ];
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('magenest_notification_type'),
            ['name', 'description','is_category','default_type','icon'],
            $data
        );
        $this->moduleDataSetup->endSetup();
    }

    /**
     * @inheirtDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheirtDoc
     */
    public static function getDependencies()
    {
        return [AddDefaultNotificationType::class];
    }
}
