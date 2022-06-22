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

namespace Magenest\MegaMenu\Model\Label;

class Validator extends \Magento\Framework\Validator\AbstractValidator
{
    const MIN_WIDTH_HEIGHT_VAL = 1;
    const MAX_WIDTH_HEIGHT_VAL = 99;

    public function isValid($value)
    {
        $data = $value->getData();
        foreach ($data as $code => $value) {
            if (!preg_match('/height$|width$|font$/', $code) || !$value) {
                continue;
            }
            if (!preg_match('/^([0-9]*[.])?[0-9]+$/', $value)) {
                $this->_addMessages([__("Only positive number allowed for width and height value(%1).", $code)]);
                continue;
            }
            if ($value < self::MIN_WIDTH_HEIGHT_VAL) {
                $this->_addMessages([__("Width/Height(%1) must be greater than or equal to %2.", $code, self::MIN_WIDTH_HEIGHT_VAL)]);
                continue;
            }
            if ($value > self::MAX_WIDTH_HEIGHT_VAL) {
                $this->_addMessages([__("Width/Height(%1) must be lesser than or equal to %2.", $code, self::MAX_WIDTH_HEIGHT_VAL)]);
            }
        }

        return empty($this->getMessages());
    }
}
