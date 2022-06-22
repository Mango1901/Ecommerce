<?php

namespace Magenest\PhotoReview\Model\Video;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface;
use Magento\Framework\Filesystem\Io\File;

class Uploader
{
    /**
     * media sub folder
     * @var string
     */
    protected $subDir = 'magenest/photoreview';
    /**
     * url builder
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;

    /**
     * @var File
     */
    protected $file;

    /**
     * @param UrlInterface $urlBuilder
     * @param Filesystem $fileSystem
     * @param File $file
     */
    public function __construct(
        UrlInterface $urlBuilder,
        Filesystem $fileSystem,
        File $file
    ) {

        $this->urlBuilder = $urlBuilder;
        $this->fileSystem = $fileSystem;
        $this->file = $file;
    }
    /**
     * get images base url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]).$this->subDir.'/video';
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getBaseDir()
    {
        return $this->fileSystem->getDirectoryWrite(DirectoryList::MEDIA)->getAbsolutePath($this->subDir.'/video');
    }

    /**
     * @param $videoUrl
     * @return bool|string|null
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveVideoFromUrl($videoUrl)
    {
        $destinationFolder = $this->getBaseDir().'/social/';
        $this->file->checkAndCreateFolder($destinationFolder);
        $pathInfo = pathinfo($videoUrl);
        $fileName = explode("?", $pathInfo['basename']);
        $newFileName = $destinationFolder . $fileName[0];
        /** read file from URL and copy it to the new destination */
        $result = $this->file->read($videoUrl, $newFileName);
        if($result)
            return '/social/'.$fileName[0];
        return $result;
    }
}
