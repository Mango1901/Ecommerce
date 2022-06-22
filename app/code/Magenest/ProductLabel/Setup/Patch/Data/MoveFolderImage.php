<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class MoveFolderImage
 * @package Magenest\ProductLabel\Setup\Patch\Data
 */
class MoveFolderImage implements DataPatchInterface
{

    const URL_ICON = 'label/tmp/image';

    const URL_ICON_DEFAULT = '/view/adminhtml/web/images/defaultImage';

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Write
     */
    protected $rootWrite;

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $rootRead;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $path;

    /**
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $dir;

    /**
     * MoveFolderImage constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param \Magento\Framework\Filesystem $filesystem
     * @param ComponentRegistrarInterface $path
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Config $eavConfig,
        \Magento\Framework\Filesystem $filesystem,
        ComponentRegistrarInterface $path,
        \Magento\Framework\Filesystem\DirectoryList $dir
    )
    {
        $this->setup = $setup;
        $this->eavConfig = $eavConfig;
        $this->filesystem = $filesystem;
        $this->rootWrite = $filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->rootRead = $filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->path = $path;
        $this->dir = $dir;
    }

    /** We'll add our customer attribute here */
    public function apply()
    {
        $rootPath = $this->path->getPath('module', 'Magenest_ProductLabel');
        $rootPub = $this->dir->getPath('media');

        if (!file_exists($rootPub .'/'. self::URL_ICON)) {
            mkdir($rootPub .'/'. self::URL_ICON, 0777, true);
        }
        $filePath = $rootPath . self::URL_ICON_DEFAULT;
        $copyFileFullPath = $rootPub .'/'. self::URL_ICON;

        $dir = opendir($filePath);
        if (!file_exists($copyFileFullPath)){
            mkdir($copyFileFullPath);
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($filePath . '/' . $file)) {
                    recurse_copy($filePath . '/' . $file, $copyFileFullPath . '/' . $file);
                } else {
                    copy($filePath . '/' . $file, $copyFileFullPath . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }
}
