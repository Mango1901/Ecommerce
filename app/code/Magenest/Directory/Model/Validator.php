<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Validator
 * @package Magenest\Directory\Model
 */
class Validator
{
    /**
     * Validator.
     *
     * @param AbstractModel $object
     * @throws \Exception
     */
    public function validate($object)
    {
        if(!$object instanceof ValidatorInterface){
            throw new \Exception('Object must instance of ValidatorInterface.');
        }

        $resource = $object->getResource();
        $connection = $resource->getConnection();
        $conditions = [];

        foreach ($object->getRequiredUniqueFields() as $field) {
            if (empty($object->getData($field))) {
                throw new \Exception(__('Field \'%1\' is required.', $field));
            }

            $conditions[] = "e.{$field} = '{$object->getData($field)}'";
        }

        $query = $connection->select()->from(['e' => $resource->getMainTable()]);
        $query->where(implode(' OR ', $conditions));

        if ($object->getId()) {
            $query->where('e.' . $resource->getIdFieldName() . ' != ' . $object->getId());
        }

        $result = $connection->fetchOne($query);
        if(false !== $result){
            throw new \Exception(__('Codes is exist.'));
        }
    }
}