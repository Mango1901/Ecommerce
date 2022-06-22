<?php
/**
 * @copyright: Copyright © 2019 Firebear Studio. All rights reserved.
 * @author   : Firebear Studio <fbeardev@gmail.com>
 */

namespace Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\VersionsCms\Model\Hierarchy\Node;

/**
 * Class Scope
 *
 * @package Firebear\ImportExport\Model\Export\ContentHierarchy\AttributeSources
 */
class Scope extends AbstractSource
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $labelDefault = ucwords(Node::NODE_SCOPE_DEFAULT);
            $labelWebsite = ucwords(Node::NODE_SCOPE_WEBSITE);
            $labelStore = ucwords(Node::NODE_SCOPE_STORE);

            $this->_options = [
                ['label' => __($labelDefault), 'value' => Node::NODE_SCOPE_DEFAULT],
                ['label' =>  __($labelWebsite), 'value' => Node::NODE_SCOPE_WEBSITE],
                ['label' =>  __($labelStore), 'value' => Node::NODE_SCOPE_STORE],
            ];
        }

        return $this->_options;
    }
}
