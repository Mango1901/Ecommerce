<?php
/**
 * Created by PhpStorm.
 * User: hahoang
 * Date: 27/01/2019
 * Time: 15:25
 */

namespace Magenest\MapList\Block\Adminhtml\Export;

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
        $this->removeButton('back')->removeButton('reset');
        $this->buttonList->update('save', 'label', __('Export'));

        $this->_objectId = 'export_id';
        $this->_blockGroup = 'Magenest_MapList';
        $this->_controller = 'adminhtml_export';
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Export');
    }
    public function getSaveUrl()
    {
        return $this->getUrl('maplist/export/export');
    }
}
