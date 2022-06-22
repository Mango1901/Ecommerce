<?php
/**
 * Created by PhpStorm.
 * User: ducquach
 * Date: 10/9/20
 * Time: 9:00 AM
 */
namespace Magenest\ElasticsearchVi\Plugin;

use Magenest\ElasticsearchVi\Model\AccentAttribute;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\AttributeProvider;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldIndex\ResolverInterface
    as FieldIndexResolver;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ResolverInterface
    as FieldTypeResolver;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldName\ResolverInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ConverterInterface
    as FieldTypeConverterInterface;
use Magento\Elasticsearch\Model\Adapter\FieldMapperInterface;


class FilterViAccent
{
    /**
     * @var FieldTypeConverterInterface
     */
    private $fieldTypeConverter;

    /**
     * @var FieldTypeResolver
     */
    private $fieldTypeResolver;

    /**
     * @var AttributeProvider
     */
    private $attributeAdapterProvider;

    /**
     * @var FieldIndexResolver
     */
    private $fieldIndexResolver;

    /**
     * @var ResolverInterface
     */
    private $fieldNameResolver;

    /**
     * @var AccentAttribute
     */
    private $accentAttribute;

    /**
     * FilterViAccent constructor.
     * @param FieldTypeConverterInterface $fieldTypeConverter
     * @param FieldIndexResolver $fieldIndexResolver
     * @param FieldTypeResolver $fieldTypeResolver
     * @param AttributeProvider $attributeAdapterProvider
     * @param ResolverInterface $fieldNameResolver
     * @param AccentAttribute $accentAttribute
     */
    public function __construct(
        FieldTypeConverterInterface $fieldTypeConverter,
        FieldIndexResolver $fieldIndexResolver,
        FieldTypeResolver $fieldTypeResolver,
        AttributeProvider $attributeAdapterProvider,
        ResolverInterface $fieldNameResolver,
        AccentAttribute $accentAttribute
    ) {
        $this->fieldTypeConverter = $fieldTypeConverter;
        $this->fieldIndexResolver = $fieldIndexResolver;
        $this->fieldTypeResolver = $fieldTypeResolver;
        $this->attributeAdapterProvider = $attributeAdapterProvider;
        $this->fieldNameResolver = $fieldNameResolver;
        $this->accentAttribute = $accentAttribute;
    }

    /**
     * Push new settings to Index
     * @param $subject
     * @param $settings
     * @return array
     */
    public function afterBuild($subject, $settings)
    {
        if (is_array($settings) && isset($settings['analysis']['analyzer']['default']['filter'])) {

            // Analyzer for accent character
            $settings['analysis']['analyzer']['non_accent_analyzer'] = $settings['analysis']['analyzer']['default'];

            // Support searching with accent character
            array_push($settings['analysis']['analyzer']['non_accent_analyzer']['filter'], 'asciifolding');

            // Support sort with accent character
            $settings['analysis']['normalizer']['non_accent_normalizer'] = [
                "filter"=> [
                    "lowercase",
                    "asciifolding"
                ]
            ];
        }
        return $settings;
    }

    /**
     * @param $subject
     * @param $fieldMapping
     * @param $attribute
     * @return mixed
     */
    public function afterGetField($subject, $fieldMapping, $attribute)
    {
        if (empty($fieldMapping)) {
            return $fieldMapping;
        }
        $attributeAdapter = $this->attributeAdapterProvider->getByAttributeCode($attribute->getAttributeCode());
        $fieldName = $this->fieldNameResolver->getFieldName($attributeAdapter);

        // Normalize sort field
        if ($attributeAdapter->isSortable() && in_array($attribute->getBackendType(),['text','varchar'])) {
            $sortFieldName = $this->fieldNameResolver->getFieldName(
                $attributeAdapter,
                ['type' => FieldMapperInterface::TYPE_SORT]
            );
            $fieldMapping[$fieldName]['fields'][$sortFieldName]['normalizer'] = 'non_accent_normalizer';
        }

        // Add non accent field for later use
        if ($this->accentAttribute->isAttributeFilterableWithAccent($attribute->getName()) &&
            null === $this->fieldIndexResolver->getFieldIndex($attributeAdapter) &&
            $this->fieldTypeResolver->getFieldType($attributeAdapter) == 'text') {

            $fieldMapping[$fieldName]['fields'][AccentAttribute::NON_ACCENT_KEY] = [
                'type' => $this->fieldTypeResolver->getFieldType($attributeAdapter),
                'analyzer' => 'non_accent_analyzer'
            ];
        }


        return $fieldMapping;
    }

}
