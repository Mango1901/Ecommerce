<?php

namespace Magenest\MapList\Model\Config\Source;

class Custom extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function getAllOptions()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * @var \Magenest\MapList\Model\ResourceModel\Location\Collection $collections
         */
        $result = array();
        $collections = $objectManager->create(\Magenest\MapList\Model\ResourceModel\Location\Collection::class);
        $stores = $collections->getData();
        foreach ($stores as $value) {
            array_push($result, array('value' => $value['location_id'], 'label' => $value['title']));
        }

        return $result;
    }
}
