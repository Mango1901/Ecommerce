<?php


namespace Magenest\Core\Plugin;


use Magento\Elasticsearch\Model\Adapter\FieldMapper\FieldMapperResolver;

class AddProductIdMapping
{

    /**
     * @param FieldMapperResolver $subject
     * @param $result
     * @param array $context
     * @return mixed
     */
    public function afterGetAllAttributesTypes(FieldMapperResolver $subject, $result, $context = [])
    {
        $result['product_id'] = [
            'type' => 'integer',
            'index' => true
        ];
        return $result;
    }
}
