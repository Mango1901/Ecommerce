<?php
namespace Magenest\CustomizePdf\Model\Filesystem\Driver;

use Magento\Framework\Exception\FileSystemException;

/**
 * Class File
 * @package Magenest\CustomizePdf\Model\Filesystem\Driver
 */
class File extends \Magento\Framework\Filesystem\Driver\File
{

    /**
     * @param $path
     * @param $findName
     * @return array|array[]
     * @throws FileSystemException
     */
    public function getFileNameDirectory($path, $findName)
    {
        try {
            $iterator = array_slice(scandir($path), 2);
            $result = [];
            foreach ($iterator as $file) {
                $fileNameArr = explode(".pdf", $file);
                $fileName = reset($fileNameArr);
                $name = utf8_encode($fileName);
                if(stristr($name, $findName)) {
                    $result[] = [
                        'value' => $name,
                        'label' => $name
                    ];
                }
            }
            return $result;
        } catch (\Exception $e) {
            throw new FileSystemException(new \Magento\Framework\Phrase($e->getMessage()), $e);
        }
    }
}
