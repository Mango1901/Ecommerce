<?php
namespace Magenest\CustomizePdf\Ui\DataProvider\Product\Form\Modifier;

use Magenest\CustomizePdf\Helper\Constant;
use Magenest\CustomizePdf\Model\Filesystem\Driver\File as DriverFile;
use Magento\Catalog\Model\Category\FileInfo;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DataSheetDataProvider
 * @package Magenest\CustomizePdf\Ui\DataProvider\Product\Form\Modifier
 */
class DataSheetDataProvider extends AbstractModifier
{
    /** @var UrlInterface  */
    protected $urlBuilder;

    /** @var ArrayManager  */
    protected $arrayManager;

    /** @var LocatorInterface  */
    protected $locator;

    /** @var DirectoryList  */
    protected $directoryList;

    /** @var DriverFile  */
    protected $driverFile;

    /** @var LoggerInterface  */
    protected $_logger;

    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * DataSheetDataProvider constructor.
     * @param UrlInterface $urlBuilder
     * @param ArrayManager $arrayManager
     * @param LocatorInterface $locator
     * @param DirectoryList $directoryList
     * @param DriverFile $driverFile
     * @param LoggerInterface $logger
     * @param FileInfo $fileInfo
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        LocatorInterface $locator,
        DirectoryList $directoryList,
        DriverFile $driverFile,
        LoggerInterface $logger,
        FileInfo $fileInfo
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->locator = $locator;
        $this->directoryList = $directoryList;
        $this->driverFile = $driverFile;
        $this->_logger = $logger;
        $this->fileInfo = $fileInfo;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $productData = &$data[$this->locator->getProduct()->getId()];
        $fileName = $productData['product']['product_instruction_pdf'] ?? false;
        if ($fileName && $this->fileInfo->isExist($fileName)) {
            $stat = $this->fileInfo->getStat($fileName);
            $mime = $this->fileInfo->getMimeType($fileName);

            $result = [
                [
                    'file_name' => $fileName,
                    'file' => $fileName,
                    'name' => basename($fileName),
                    'url'  => $this->urlBuilder->getBaseUrl() . 'pub/' . $fileName,
                    'size' => $stat['size'],
                    'type' => $mime
                ]
            ];
            $productData['product']['product_instruction_pdf'] = $result;
        } else {
            unset($productData['product']['product_instruction_pdf']);
        }
        return $data;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $this->getPdfFileNameModal($meta);
        $this->getPdfFileUploader($meta);
        return $meta;
    }

    /**
     * @param $meta
     * @return array
     */
    public function getPdfFileNameModal($meta)
    {
        $fieldCode = Constant::DATASHEET_CODE;
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $meta, null, 'children');
        $fieldIsDisabled = $this->locator->getProduct()->isLockedAttribute($fieldCode);

        if (!$elementPath) {
            return $meta;
        }
        $value = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => false,
                        'required' => false,
                        'dataScope' => '',
                        'breakLine' => false,
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'component' => 'Magento_Ui/js/form/components/group',
                        'disabled' => $this->locator->getProduct()->isLockedAttribute($fieldCode),
                    ],
                ],
            ],
            'children' => [
                $fieldCode => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'select',
                                'componentType' => 'field',
                                'component' => 'Magenest_CustomizePdf/js/ui/form/element/select-box',
                                'filterOptions' => true,
                                'chipsEnabled' => true,
                                'disableLabel' => true,
                                'levelsVisibility' => '1',
                                'disabled' => $fieldIsDisabled,
                                'elementTmpl' => 'Magenest_CustomizePdf/ui/form/element/input-search',
                                'notice' => __('Please enter at least 3 characters to show the suggestions.'),
                                'listens' => [
                                    'index=create_category:responseData' => 'setParsed',
                                    'newOption' => 'toggleOptionSelected'
                                ],
                                'config' => [
                                    'dataScope' => $fieldCode,
                                    'sortOrder' => 10,
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        ];
        $meta = $this->arrayManager->merge($containerPath, $meta, $value);

        return $meta;
    }

    public function getPdfFileUploader($meta)
    {
        $fieldCode = 'product_instruction_pdf';
        $elementPath = $this->arrayManager->findPath($fieldCode, $meta, null, 'children');

        if (!$elementPath) {
            return $meta;
        }
        $value = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Product Instruction PDF'),
                        'required' => false,
                        'maxFileSize' => 52428800,
                        'formElement' => 'fileUploader',
                        'componentType' => 'field',
                        'component' => 'Magenest_CustomizePdf/js/ui/form/element/file-uploader',
                        'template' => 'Magenest_CustomizePdf/ui/form/element/pdf-uploader',
                        'previewTmpl' => 'Magenest_CustomizePdf/form/element/uploader/preview',
                        'isMultipleFiles' => false,
                        'allowedExtensions' => 'pdf',
                        'uploaderConfig' => [
                            'url' => $this->urlBuilder->getUrl('magenest/product/upload')
                        ],
                        'validation' => [
                            'file_extensions' => 'pdf',
                        ],
                    ],
                ],
            ]
        ];
        $meta = $this->arrayManager->merge($elementPath, $meta, $value);

        return $meta;
    }
}
