<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MapList\Block\Adminhtml\Import\Edit;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Basic import model
     *
     */

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Add fieldsets
     *
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create(
            array(
                'data' =>
                    array(
                        'id' => 'edit_form',
                        'action' => $this->getData('action'),
                        'method' => 'post',
                        'enctype' => 'multipart/form-data'
                    )
            )
        );
        $form->setHtmlIdPrefix('map_');

        $form->setUseContainer(true);

        $fieldset = $form->addFieldset(
            'setting_fieldset',
            array(
                'legend' => __('Import Settings'),
                'class' => 'fieldset-wide'
            )
        );
        $fieldset->addField(
            'entity',
            'select',
            array(
                'name' => 'entity',
                'title' => __('Entity Type'),
                'label' => __('Entity Type'),
                'required' => true,
                'onchange' => '',
                'values' => array(0=>"Store Locator and MapList"),
                'after_element_html' => $this->getDownloadSampleFileHtml(),
            )
        );

        $fieldsets['upload'] = $form->addFieldset(
            'upload_file_fieldset',
            array('legend' => __('File to Import'))
        );
        $fieldsets['upload']->addField(
            'import_map',
            'file',
            array(
                'name' => 'import_map',
                'label' => __('Select File to Import'),
                'title' => __('Select File to Import'),
                'required' => true,
                'class' => 'input-file'
            )
        );
//        $fieldsets['upload']->addField(
//            'path_folder_image',
//            'text',
//            [
//                'name' => 'path_folder_image',
//                'label' => __('Images File Directory'),
//                'title' => __('Images File Directory'),
//                'required' => false,
//                'class' => 'input-text',
//                'note' => __(
//                    'Please upload image into folder "/pub/media/catalog/category" before import !'
//                ),
//            ]
//        );

        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * Get download sample file html
     *
     * @return string
     */
    protected function getDownloadSampleFileHtml()
    {
        $html = '<span id="sample-file-span" class="display"><a id="sample-file-link" href="'.$this->_urlBuilder->getUrl('maplist/import/download/filename/import_maplist_sample_data/').'" style="margin-left: 15px;">'
            . __('Download Sample File')
            . '</a></span>';
        return $html;
    }
}
