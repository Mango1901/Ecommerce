<?php

namespace Magenest\CustomCatalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Container;

class CustomSearchEngineOptimization extends AbstractModifier
{
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        if ($this->locator->getProduct()->getId()) {
            $meta = $this->addCountInputField($meta);
        }

        return $meta;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Adding URL rewrite checkbox to meta
     *
     * @param array $meta
     * @return array
     */
    protected function addCountInputField(array $meta)
    {
        $listeners = [
            ProductAttributeInterface::CODE_SEO_FIELD_META_TITLE,
            ProductAttributeInterface::CODE_SEO_FIELD_META_KEYWORD,
            ProductAttributeInterface::CODE_SEO_FIELD_META_DESCRIPTION,
        ];

        foreach ($listeners as $listener) {
            $urlPath = $this->arrayManager->findPath(
                $listener,
                $meta,
                null,
                'children'
            );
            if ($urlPath) {
                $containerPath = $this->arrayManager->slicePath($urlPath, 0, -2);

                $label = $this->arrayManager->get($containerPath .  static::META_CONFIG_PATH . '/label', $meta);
                $titleKey = $this->locator->getProduct()->getData($listener);

                $metaCount['arguments']['data']['config'] = [
                    'componentType' => Container::NAME,
                    'component' => 'Magenest_CustomCatalog/js/components/count-text',
                    'additionalClasses' => 'note_field',
                    'content' => __('Length Of Meta Title: $1/255', strlen($titleKey)),
                    'label' => $label,
                    'imports' => [
                        'titleKey' => '${ $.provider }:data.product.' . $listener,
                        'handleChanges' => '${ $.provider }:data.product.' . $listener,
                        '__disableTmpl' => ['titleKey' => false, 'handleChanges' => false],
                    ],
                    'dataScope' => $listener . '_count',
                ];

                $meta = $this->arrayManager->merge(
                    $urlPath . static::META_CONFIG_PATH,
                    $meta,
                    ['valueUpdate' => 'keyup']
                );
                $meta = $this->arrayManager->merge(
                    $containerPath . '/children',
                    $meta,
                    [$listener . '_count' => $metaCount]
                );
            }
        }

        return $meta;
    }
}
