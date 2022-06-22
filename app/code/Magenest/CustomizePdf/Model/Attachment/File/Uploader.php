<?php
namespace Magenest\CustomizePdf\Model\Attachment\File;

use Magenest\CustomizePdf\Helper\Constant;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Catalog\Model\Product\Media\Config;

/**
 * Class Uploader
 * @package Magenest\CustomizePdf\Model\Attachment\File
 */
class Uploader
{
    /**
     * @var UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var string[]
     */
    private $allowedExtensions;

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var Config
     */
    private $config;

    /**
     * Uploader constructor.
     * @param UploaderFactory $uploaderFactory
     * @param Filesystem $fileSystem
     * @param Config $config
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        Filesystem $fileSystem,
        Config $config
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $fileSystem;
        $this->config = $config;
    }

    /**
     * Save file to temp directory
     *
     * @param string $fileId
     * @return array
     */
    public function saveToTmpFolder($fileId)
    {
        try {
            $result = ['file' => '', 'size' => '', 'name' => '', 'path' => '', 'type' => ''];
            $mediaDirectory = $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)
                ->getAbsolutePath(Constant::DATASHEET_PATH);
            /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
            $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
            $uploader->setAllowRenameFiles(true)
                ->setAllowedExtensions($this->getAllowedExtensions());
            $result = array_intersect_key($uploader->save($mediaDirectory), $result);

            $result['url'] = implode("/", [$this->config->getBaseMediaUrl(), Constant::DATASHEET_PATH, $result['file']]);
            $result['file_name'] = $result['file'];
            $result['id'] = base64_encode($result['file_name']);
            $result['file_path'] = implode("/", ['media', Constant::DATASHEET_PATH, $result['file']]);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $result;
    }

    /**
     * Set allowed extensions
     * @param string[] $allowedExtensions
     * @return $this
     */
    public function setAllowedExtensions($allowedExtensions)
    {
        $this->allowedExtensions = $allowedExtensions;
        return $this;
    }

    /**
     * Retrieve allowed extensions
     *
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return $this->allowedExtensions;
    }
}
