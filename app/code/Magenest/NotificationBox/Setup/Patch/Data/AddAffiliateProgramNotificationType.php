<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 13/06/2022
 * Time: 15:06
 */
declare(strict_types=1);

namespace Magenest\NotificationBox\Setup\Patch\Data;

use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\Notification;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AddAffiliateProgramNotificationType
 */
class AddAffiliateProgramNotificationType implements DataPatchInterface
{
    /**
     * @var DirectoryList
     */
    protected $dir;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $path;
    /**
     * @varHelper
     */
    private $helper;
    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;
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
        ModuleDataSetupInterface    $moduleDataSetup,
        Helper                      $helper,
        StoreManagerInterface       $storeManagerInterface,
        DirectoryList               $dir,
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
    public static function getDependencies()
    {
        return [AddDefaultNotificationType::class];
    }

    /**
     * @inheirtDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $rootPath = $this->path->getPath('module', 'Magenest_NotificationBox');
        // Get media folder
        $rootPub = $this->dir->getPath('media');

        // Create and authorize the notificationtype/icon directory
        if (!file_exists($rootPub . '/' . AddDefaultNotificationType::URL_ICON)) {
            mkdir($rootPub . '/' . AddDefaultNotificationType::URL_ICON, 0777, true);
        }
        $filePath = $rootPath . AddDefaultNotificationType::URL_ICON_DEFAULT;
        $copyFileFullPath = $rootPub . '/' . AddDefaultNotificationType::URL_ICON;

        //copy folder images to pub/media folder
        $this->helper->copyDirectory($filePath, $copyFileFullPath);

        $currentStore = $this->storeManagerInterface->getStore();

        $mediaUrl = $currentStore->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . AddDefaultNotificationType::URL_ICON;

        $listDefaultImage = $this->helper->getDefaultImage();

        $data = [
            [
                'name' => Notification::AFFILIATE_PROGRAM_LABEL,
                'description' => Notification::AFFILIATE_PROGRAM_LABEL,
                'is_category' => 1,
                'default_type' => Notification::AFFILIATE_PROGRAM,
                'icon' => '[{
                    "name": "' . Notification::AFFILIATE_PROGRAM . '",
                    "type": "image/png",
                    "url": "' . $mediaUrl . $listDefaultImage[Notification::AFFILIATE_PROGRAM] . '",
                    "size":"1093"
                }]'
            ]
        ];
        $this->moduleDataSetup->getConnection()->insertArray(
            $this->moduleDataSetup->getTable('magenest_notification_type'),
            ['name', 'description', 'is_category', 'default_type', 'icon'],
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
}
