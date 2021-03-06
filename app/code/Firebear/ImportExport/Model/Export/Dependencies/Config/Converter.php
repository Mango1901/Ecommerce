<?php
/**
 * @copyright: Copyright © 2017 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\Dependencies\Config;

use Firebear\ImportExport\Model\Source\Factory as SourceFactory;
use InvalidArgumentException;
use Magento\Framework\App\Utility\Classes;
use Magento\Framework\Config\ConverterInterface;
use Magento\Framework\Module\Manager;
use DOMDocument;
use DOMNode;

/**
 * Class Converter
 *
 * @package Firebear\ImportExport\Model\Export\Dependencies\Config
 */
class Converter implements ConverterInterface
{

    /**
     * @var SourceFactory
     */
    protected $createFactory;

    /**
     * @var Manager
     */
    protected $manager;

    /**
     * Converter constructor.
     * @param SourceFactory $createFactory
     * @param Manager $moduleManager
     */
    public function __construct(
        SourceFactory $createFactory,
        Manager $moduleManager
    ) {
        $this->createFactory = $createFactory;
        $this->manager = $moduleManager;
    }

    /**
     * Convert dom node tree to array
     *
     * @param DOMDocument $source
     * @return array
     * @throws InvalidArgumentException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source)
    {
        $result = [];
        /** @var DOMNode $templateNode */
        foreach ($source->documentElement->childNodes as $typeNode) {
            if ($typeNode->nodeType != XML_ELEMENT_NODE) {
                continue;
            }
            $entityName = $typeNode->attributes->getNamedItem('name')->nodeValue;
            $typeLabel = $typeNode->attributes->getNamedItem('label')->nodeValue;
            $model = $typeNode->attributes->getNamedItem('model')->nodeValue;
            $sortOrder = ($typeNode->attributes->getNamedItem('sortOrder'))
                ? $typeNode->attributes->getNamedItem('sortOrder')->nodeValue
                : null;
            $depends = ($typeNode->attributes->getNamedItem('depends'))
                ? $typeNode->attributes->getNamedItem('depends')->nodeValue
                : null;

            if (!$this->manager->isEnabled(Classes::getClassModuleName($model)) ||
                ($depends && !$this->manager->isEnabled(Classes::getClassModuleName($depends)))) {
                continue;
            }

            $result[$entityName] = [
                'label' => $typeLabel,
                'model' => $model,
                'sort_order' => $sortOrder
            ];
            foreach ($typeNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $result[$entityName]['fields'][$childNode->attributes->getNamedItem('name')->nodeValue] = [
                    'label' => $childNode->attributes->getNamedItem('label')->nodeValue,
                    'model' => $childNode->attributes->getNamedItem('model')->nodeValue,
                    'main_field' => $childNode->attributes->getNamedItem('main_field')->nodeValue,
                    'parent' => ($childNode->attributes->getNamedItem('parent'))
                        ? $childNode->attributes->getNamedItem('parent')->nodeValue
                        : null,
                    'parent_field' => ($childNode->attributes->getNamedItem('parent_field'))
                        ? $childNode->attributes->getNamedItem('parent_field')->nodeValue
                        : null,
                ];
                foreach ($childNode->childNodes as $field) {
                    if ($field->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    $options = [];
                    if ($field->attributes->getNamedItem('model')) {
                        $model = $this->createFactory->create($field->attributes->getNamedItem('model')->nodeValue);
                        $options = $model->toOptionArray();
                    }
                    $delete = $field->attributes->getNamedItem('delete')
                        ? $field->attributes->getNamedItem('delete')->nodeValue : 0;
                    $result[$entityName]['fields']
                    [$childNode->attributes->getNamedItem('name')->nodeValue]['fields']
                    [$field->attributes->getNamedItem('name')->nodeValue] =
                        [
                            'type' => $field->attributes->getNamedItem('type')->nodeValue,
                            'options' => $options,
                            'delete' => $delete
                        ];
                }
            }
        }

        return $result;
    }
}
