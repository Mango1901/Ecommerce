<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class MenuBrief
 *
 * @package Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources
 */
class MenuBrief extends AbstractSource
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['value' => 1, 'label' => __('Only Children')],
                ['value' => 0, 'label' => __('Neighbours and Children')]
            ];
        }

        return $this->_options;
    }
}
