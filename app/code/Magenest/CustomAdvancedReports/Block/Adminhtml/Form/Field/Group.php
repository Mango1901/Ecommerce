<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\CustomAdvancedReports\Block\Adminhtml\Form\Field;

use Magenest\CustomAdvancedReports\Block\Adminhtml\Form\Field\Category;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class CountryCreditCard
 */
class Group extends AbstractFieldArray
{

    protected $ccTypesRenderer = null;



    /**
     * Prepare to render
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn('name', ['label' => __('Group Name'), 'class' => 'required-entry']);

        $this->addColumn(
            'category',
            [
                'label' => __('Category'),
                'renderer' => $this->getCcTypesRenderer(),
                'class' => 'required-entry'
            ]
        );
        $this->_addAfter       = false;
        $this->_addButtonLabel = __('Add Group');
    }

    /**
     * @return Category|BlockInterface
     * @throws LocalizedException
     */
    protected function getCcTypesRenderer()
    {
        if (!$this->ccTypesRenderer) {
            $this->ccTypesRenderer = $this->getLayout()->createBlock(
                Category::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->ccTypesRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param DataObject $row
     * @return void
     * @throws LocalizedException
     */
    protected function _prepareArrayRow(DataObject $row)
    {
        $country = $row->getName();
        $options = [];
        if ($country) {

            $ccTypes = $row->getCategory();
            foreach ($ccTypes as $cardType) {
                $options['option_' . $this->getCcTypesRenderer()->calcOptionHash($cardType)]
                    = 'selected="selected"';
            }
        }
        $row->setData('option_extra_attrs', $options);
    }
}
