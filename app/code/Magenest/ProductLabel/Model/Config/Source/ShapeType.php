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

namespace Magenest\ProductLabel\Model\Config\Source;

use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\UrlInterface;

/**
 * Class ShapeType
 * @package Magenest\ProductLabel\Model\Config\Source
 */
class ShapeType extends \Magento\Framework\Data\Form\Element\File implements ArrayInterface
{
    const PRODUCT_LABEL_MEDIA_PATH = 'label/tmp/image/';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var string[]
     */
    private $shapeTypes = [
        'shape-new-1' => 'Shape–1',
        'shape-new-2' => 'Shape–2',
        'shape-new-3' => 'Shape–3',
        'shape-new-4' => 'Shape–4',
        'shape-new-5' => 'Shape–5',
        'shape-new-6' => 'Shape–6',
        'shape-new-7' => 'Shape–7',
        'shape-new-8' => 'Shape–8',
        'shape-new-9' => 'Shape–9',
        'shape-new-10' => 'Shape–10',
        'shape-new-11' => 'Shape–11',
        'shape-new-12' => 'Shape–12',
        'shape-new-13' => 'Shape–13',
        'shape-new-14' => 'Shape–14',
        'shape-new-15' => 'Shape–15',
        'shape-new-16' => 'Shape–16',
        'shape-new-17' => 'Shape–17',
        'shape-new-18' => 'Shape–18',
        'shape-new-19' => 'Shape–19',
        'shape-new-20' => 'Shape–20',
    ];

    /**
     * ShapeType constructor.
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        Context $context,
        $data = []
    )
    {
        $this->context = $context;
        $this->urlBuilder = $context->getUrlBuilder();
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
    }

    public function toOptionArray()
    {
        $shapes = $this->shapeTypes;
        $data = [];
        foreach ($shapes as $shape => $shapeName) {
            $svg = $this->getLabelPath()  . $shapeName . '.svg';
            $data[] = ['value' => $shape, 'label' => $svg];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getLabelPath()
    {
        $path = $this->urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]);
        $path .= self::PRODUCT_LABEL_MEDIA_PATH;

        return $path;
    }

}
