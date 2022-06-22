<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Setup;

use Magento\Eav\Setup\EavSetup;

class MenuEntitySetup extends EavSetup
{
    public function getDefaultEntities()
    {
        /*	#snippet1	*/
        $menuEntityEntity = \Magenest\MegaMenu\Model\MenuEntity::ENTITY;
        $entities = [
            $menuEntityEntity => [
                'entity_model' => 'Magenest\MegaMenu\Model\ResourceModel\MenuEntity',
                'table' => $menuEntityEntity . '_entity',
                'attributes' => [
                    'menu_id' => [
                        'type' => 'static',
                    ],
                    'label_name' => [
                        'type' => 'static',
                    ],
                    'level' => [
                        'type' => 'static',
                    ],
                    'menu_type' => [
                        'type' => 'static',
                    ],
                    'is_mega' => [
                        'type' => 'static',
                    ],
                ],
            ],
        ];

        return $entities;
        //end
    }
}
