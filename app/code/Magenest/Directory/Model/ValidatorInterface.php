<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model;

/**
 * Interface ValidatorInterface
 * @package Magenest\Directory\Model
 */
interface ValidatorInterface
{
    /**
     * Get require fields
     *
     * @return array
     */
    public function getRequiredUniqueFields();
}