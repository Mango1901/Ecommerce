<?php
namespace Magenest\ElasticsearchVi\Model\Adapter\FieldMapper;

use Magento\Elasticsearch\Model\Adapter\FieldsMappingPreprocessorInterface;

class AddDefaultSearchNonAccentField implements FieldsMappingPreprocessorInterface
{

    public function process(array $mapping): array
    {
        if (isset($mapping['_search'])) {
            $mapping['_search']['fields']['non_accent'] = [
                'type' => 'text',
                'analyzer' => 'non_accent_analyzer'
            ];
        }
        return $mapping;
    }
}
