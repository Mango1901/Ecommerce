<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Model;

/**
 * Class Standard
 * @package Magenest\CustomTableRate\Model
 */
class KangarooTableRates extends Carrier
{
    /** @const */
    const CODE = 'kangaroo_tablerates';

    /**
     * @var string
     */
    protected $_code = self::CODE;
}
