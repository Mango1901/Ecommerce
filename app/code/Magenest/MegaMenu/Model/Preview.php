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

namespace Magenest\MegaMenu\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Preview
 * @package Magenest\MegaMenu\Model
 *
 * @method string getToken()
 */
class Preview extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'magenest_menu_preview';

    /**
     * @var string
     */
    protected $_eventPrefix = 'magenest_menu_preivew';

    /**
     * @var string
     */
    protected $_eventObject = 'object';

    /**
     * @inheritdoc
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magenest\MegaMenu\Model\ResourceModel\Preview');
    }
}
