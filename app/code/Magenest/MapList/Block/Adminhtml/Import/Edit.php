<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Import edit block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magenest\MapList\Block\Adminhtml\Import;

/**
 * @api
 * @since 100.0.2
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Import'));
        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Magenest_MapList';
        $this->_controller = 'adminhtml_import';
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import');
    }
    public function getSaveUrl()
    {
        return $this->getUrl('maplist/import/import');
    }
}
