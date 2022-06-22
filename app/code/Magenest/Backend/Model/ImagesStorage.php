<?php
namespace Magenest\Backend\Model;

use Magento\Backend\Model\Session;
use Magento\Backend\Model\UrlInterface;
use Magento\Cms\Helper\Wysiwyg\Images;
use Magento\Cms\Model\Wysiwyg\Images\Storage;
use Magento\Cms\Model\Wysiwyg\Images\Storage\CollectionFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\Image\AdapterFactory;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaStorage\Helper\File\Storage\Database;
use Magento\MediaStorage\Model\File\Storage\Directory\DatabaseFactory;
use Magento\MediaStorage\Model\File\Storage\FileFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Psr\Log\LoggerInterface;

class ImagesStorage extends Storage
{
    /** @var Mime */
    private $mime;

    /** @var LoggerInterface */
    private $logger;

    /**
     * @param Session $session
     * @param UrlInterface $backendUrl
     * @param Images $cmsWysiwygImages
     * @param Database $coreFileStorageDb
     * @param Filesystem $filesystem
     * @param AdapterFactory $imageFactory
     * @param Repository $assetRepo
     * @param CollectionFactory $storageCollectionFactory
     * @param FileFactory $storageFileFactory
     * @param \Magento\MediaStorage\Model\File\Storage\DatabaseFactory $storageDatabaseFactory
     * @param DatabaseFactory $directoryDatabaseFactory
     * @param UploaderFactory $uploaderFactory
     * @param DriverInterface $file
     * @param File $ioFile
     * @param LoggerInterface $logger
     * @param Mime $mime
     * @param array $resizeParameters
     * @param array $extensions
     * @param array $dirs
     * @param array $data
     *
     * @throws FileSystemException
     */
    public function __construct(
        Session                                                  $session,
        UrlInterface                                             $backendUrl,
        Images                                                   $cmsWysiwygImages,
        Database                                                 $coreFileStorageDb,
        Filesystem                                               $filesystem,
        AdapterFactory                                           $imageFactory,
        Repository                                               $assetRepo,
        CollectionFactory                                        $storageCollectionFactory,
        FileFactory                                              $storageFileFactory,
        \Magento\MediaStorage\Model\File\Storage\DatabaseFactory $storageDatabaseFactory,
        DatabaseFactory                                          $directoryDatabaseFactory,
        UploaderFactory                                          $uploaderFactory,
        DriverInterface                                          $file,
        File                                                     $ioFile,
        LoggerInterface                                          $logger,
        Mime                                                     $mime,
        array                                                    $resizeParameters = [],
        array                                                    $extensions = [],
        array                                                    $dirs = [],
        array                                                    $data = []
    ) {
        $this->mime   = $mime;
        $this->logger = $logger;
        parent::__construct(
            $session,
            $backendUrl,
            $cmsWysiwygImages,
            $coreFileStorageDb,
            $filesystem,
            $imageFactory,
            $assetRepo,
            $storageCollectionFactory,
            $storageFileFactory,
            $storageDatabaseFactory,
            $directoryDatabaseFactory,
            $uploaderFactory,
            $resizeParameters,
            $extensions,
            $dirs,
            $data,
            $file,
            $ioFile,
            $logger,
            $mime
        );
    }

    public function getFilesCollection($path, $type = null)
    {
        if ($this->_coreFileStorageDb->checkDbUsage()) {
            $files = $this->_storageDatabaseFactory->create()->getDirectoryFiles($path);

            $fileStorageModel = $this->_storageFileFactory->create();
            foreach ($files as $file) {
                $fileStorageModel->saveFile($file);
            }
        }

        $collection = $this->getCollection($path)
            ->setCollectDirs(false)
            ->setCollectFiles(true)
            ->setCollectRecursively(false)
            ->setOrder('mtime', Collection::SORT_ORDER_DESC);

        // Add files extension filter
        if ($allowed = $this->getAllowedExtensions($type)) {
            $collection->setFilesFilter('/\.(' . implode('|', $allowed) . ')$/i');
        }

        foreach ($collection as $item) {
            $item->setId($this->_cmsWysiwygImages->idEncode($item->getBasename()));
            $item->setName($item->getBasename());
            $item->setShortName($this->_cmsWysiwygImages->getShortFilename($item->getBasename()));
            $item->setUrl($this->_cmsWysiwygImages->getCurrentUrl() . $item->getBasename());
            $driver    = $this->_directory->getDriver();
            $itemStats = $driver->stat($item->getFilename());
            $item->setSize($itemStats['size']);
            $mimeType = $itemStats['mimetype'] ?? $this->mime->getMimeType($item->getFilename());
            $item->setMimeType($mimeType);

            if ($this->isImage($item->getBasename())) {
                $thumbUrl = $this->getThumbnailUrl($item->getFilename(), true);
                // generate thumbnail "on the fly" if it does not exists
                if (!$thumbUrl) {
                    $thumbUrl = $this->_backendUrl->getUrl('cms/*/thumbnail', ['file' => $item->getId()]);
                }

                try {
                    $size = getimagesizefromstring(
                        $driver->fileGetContents($item->getFilename())
                    );

                    if (is_array($size)) {
                        $item->setWidth($size[0]);
                        $item->setHeight($size[1]);
                    }
                } catch (\Error $e) {
                    $this->logger->notice(sprintf("GetImageSize caused error: %s", $e->getMessage()));
                }
            } else {
                $thumbUrl = $this->_assetRepo->getUrl(self::THUMB_PLACEHOLDER_PATH_SUFFIX);
            }

            $item->setThumbUrl($thumbUrl);
        }

        return $collection;
    }
}
