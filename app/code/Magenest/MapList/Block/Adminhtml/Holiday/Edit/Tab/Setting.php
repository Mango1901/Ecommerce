<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 16/09/2016
 * Time: 10:14
 */

namespace Magenest\MapList\Block\Adminhtml\Holiday\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magenest\MapList\Model\Status;

class Setting extends Generic implements TabInterface
{
    protected $_wysiwygConfig;
    protected $_status;
    protected $_locationFactory;
    protected $regionColFactory;
    protected $_store;


    public function __construct(
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Directory\Model\RegionFactory $regionColFactory,
        \Magento\Store\Model\System\Store $store,
        array $data
    )
    {
        $this->_locationFactory = $locationFactory;
        $this->_status = $status;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->regionColFactory = $regionColFactory;
        $this->_store = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('holiday');
        $data = $model->getData();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('holiday_');

        $fieldset = $form->addFieldset(
            'setting_fieldset',
            array(
                'legend' => __('General Settings'),
                'class' => 'fieldset-wide'
            )
        );

        if ($model->getId()) {
            $fieldset->addField(
                'holiday_id',
                'hidden',
                array('name' => 'holiday_id')
            );
        }

        $fieldset->addField(
            'holiday_name',
            'text',
            array(
                'name' => 'holiday_name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
                'class' => 'validate-no-html-tags',
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
            'date',
            'date',
            array(
                'label' => __('Date'),
                'title' => __('Date'),
                'name' => 'date',
                'date_format' => 'Y-MM-dd',
                'required' => true,
                'time' => false,
            )
        );
        $fieldset->addField(
            'holiday_date_to',
            'date',
            array(
                'label' => __('Holiday Date To'),
                'title' => __('Holiday Date To'),
                'date_format' => 'Y-MM-dd',
                'name' => 'holiday_date_to',
                'required' => false,
                'time' => false,
            )
        );

        $fieldset->addField(
            'comment',
            'text',
            array(
                'label' => __('Comment'),
                'title' => __('Comment'),
                'required' => false,
                'class' => 'validate-no-html-tags',
            )
        );

        if (!isset($data['status'])) {
            $data['status'] = 1;
        }

        $form->setValues($data);
        $this->setForm($form);


        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Holiday Settings');
    }

    public function getTabTitle()
    {
        return __('Holiday Settings');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }


    private function _getAllStore()
    {
        // Get our collection
        $existingStore = $this->_locationFactory->create()->getCollection()->getData();
        $storeList = array();
        foreach ($existingStore as $store) {
            $productList[] = array(
                'value' => $store['location_id'],
                'label' => $store['name']
            );
        }

        return $storeList;
    }

}
