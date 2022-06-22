<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 16/09/2016
 * Time: 09:22
 */

namespace Magenest\MapList\Block\Adminhtml\Brand\Edit;
use Magenest\MapList\Model\Status;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_status;
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        Status $status,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = array()
    ) {
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_status = $status;
        parent::__construct($context, $registry, $formFactory, $data);
    }
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('brand');
        $data = $model->getData();
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
        $form->setHtmlIdPrefix('brand_');
        $fieldset = $form->addFieldset(
            'setting_fieldset',
            array(
                'class' => 'fieldset-wide'
            )
        );
        $fieldset->addType('logo', \Magenest\MapList\Block\Adminhtml\Brand\Helper\Logo::class);
        if ($model->getId()) {
            $fieldset->addField(
                'brand_id',
                'hidden',
                array('name' => 'brand_id')
            );
        }

        $fieldset->addField(
            'name',
            'text',
            array(
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'class' => 'validate-alphanum-with-spaces'
            )
        );
        $fieldset->addField(
            'status',
            'select',
            array(
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'status',
                'required' => false,
                'options' => $this->_status->getOptionArray(),
            )
        );
        $fieldset->addField(
            'logo',
            'logo',
            array(
                'name'     => 'logo',
                'label'    => __('Brand Logo'),
                'title'    => __('Brand Logo'),
                'required' => false,
                'note' => 'Allow image type: jpg, jpeg, gif, png (Optimal icon min size is 100 x 100 px)',
            )
        );
        $fieldset->addField(
            'description',
            'editor',
            array(
                'name' => 'description',
                'label' => __('Brand description'),
                'title' => __('Brand description'),
                'style' => 'height:20em',
                'required' => false,
                'config' => $this->_wysiwygConfig->getConfig(),
            )
        );
        if (!isset($data['status'])) {
            $data['status'] = 1;
        }
        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
