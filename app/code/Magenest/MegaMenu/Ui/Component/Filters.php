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

namespace Magenest\MegaMenu\Ui\Component;

class Filters extends \Magento\Ui\Component\Filters
{
    protected $filterMap = [
        'text' => 'filterInput',
        'textRange' => 'filterRange',
        'numberRange' => 'filterRange',
        'select' => 'filterSelect',
        'dateRange' => 'filterDate',
    ];
}
