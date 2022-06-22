<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\CustomCatalog\Controller\Adminhtml\Category;

use Magenest\CustomCatalog\Setup\Patch\Data\AddIncludeInCategoryMenu;

/**
 * Class Save
 */
class Save extends \Magento\Catalog\Controller\Adminhtml\Category\Save
{
    /**
     * @inheritDoc
     */
    public function stringToBoolConverting($data, $stringToBoolInputs = null)
    {
        if (!isset($this->stringToBoolInputs[AddIncludeInCategoryMenu::ATTRIBUTE_CODE])) {
            array_push($this->stringToBoolInputs, AddIncludeInCategoryMenu::ATTRIBUTE_CODE);
        }

        return parent::stringToBoolConverting($data, $stringToBoolInputs);
    }
}
