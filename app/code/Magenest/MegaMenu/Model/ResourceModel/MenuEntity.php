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

namespace Magenest\MegaMenu\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magenest\MegaMenu\Model\MenuEntity as Model;

/**
 * Class MenuEntity
 * @package Magenest\MegaMenu\Model\ResourceModel
 */
class MenuEntity extends AbstractEntity
{
    /**
     * @inheritdoc
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(Model::ENTITY);
        }

        return parent::getEntityType();
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_read = 'magenest_menu_read';
        $this->_write = 'magenest_menu_write';
    }
}
