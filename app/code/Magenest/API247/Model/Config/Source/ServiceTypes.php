<?php


namespace Magenest\API247\Model\Config\Source;


use Magenest\API247\Model\API247Post;
use Magento\Framework\Option\ArrayInterface;

class ServiceTypes implements ArrayInterface
{
    private API247Post $API247Post;
    public function __construct(API247Post $API247Post)
    {
        $this->API247Post = $API247Post;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $data = [];
        try {
            $API247 = $this->API247Post->connect();
            $getServiceTypes = $API247->getCustomerServiceTypes();
        } catch (\Exception $e) {
            return [];
        }

        foreach($getServiceTypes as $one){
            $data[] = [
              'value' => $one['ServiceTypeID'],
              'label' => $one['ServiceTypeName']
            ];
        }
        return $data;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [0 => __('Production'), 1 => __('Sandbox Staging')];
    }
}
