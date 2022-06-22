<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 16/09/2016
 * Time: 10:14
 */

namespace Magenest\MapList\Block\Adminhtml\Location\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magenest\MapList\Model\Status;
use \Magento\Directory\Model\Config\Source\Country;

class Setting extends Generic implements TabInterface
{
    protected $_wysiwygConfig;
    protected $_status;
    protected $_productFactory;
    protected $_country;
    protected $regionColFactory;
    protected $_store;


    public function __construct(
        \Magento\Catalog\Model\ProductFactory $productFactory,
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Status $status,
        Country $country,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Directory\Model\RegionFactory $regionColFactory,
        \Magento\Store\Model\System\Store $store,
        array $data
    ) {
        $this->_productFactory = $productFactory;
        $this->_status = $status;
        $this->_country = $country;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->regionColFactory     = $regionColFactory;
        $this->_store = $store;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('maplist_location_edit');
        $data = $model->getData();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('location_');

        $fieldset = $form->addFieldset(
            'setting_fieldset',
            array(
                'legend' => __('General Settings'),
                'class' => 'fieldset-wide'
            )
        );

        $fieldset->addType('map', '\Magenest\MapList\Block\Adminhtml\Template\GoogleMap');
        $fieldset->addType('customgallery', '\Magenest\MapList\Block\Adminhtml\Template\Gallery');
        $fieldset->addType('customicon', '\Magenest\MapList\Block\Adminhtml\Template\Icon');
        $fieldset->addType('customaddress', '\Magenest\MapList\Block\Adminhtml\Template\Address');


        $countries = $this->_country->toOptionArray(false, 'US');



        if ($model->getId()) {
            $fieldset->addField(
                'location_id',
                'hidden',
                array('name' => 'location_id')
            );
        }

        $fieldset->addField(
            'is_active',
            'select',
            array(
                'label' => __('Status'),
                'title' => __('Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => array('1' => __('Enable'), '0' => __('Disable'))
            )
        );

        if (!isset($data['is_active'])) {
            $data['is_active'] = 1;
        }

        $fieldset->addField(
            'title',
            'text',
            array(
                'name' => 'title',
                'label' => __('Name Store'),
                'title' => __('Name'),
                'required' => true
            )
        );

        $fieldset->addField(
            'short_description',
            'textarea',
            array(
                'name' => 'short_description',
                'label' => __('Short Description'),
                'title' => __('short_description'),
            )
        );

        $fieldset->addField(
            'description',
            'editor',
            array(
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'style' => 'height:20em',
                'required' => false,
                'config' => $this->_wysiwygConfig->getConfig(),
            )
        );

        $fieldset->addField(
            'gallery_image',
            'customgallery',
            array(
                'name' => 'gallery_image',
                'label' => __('Gallery Image'),
                'title' => __('Gallery Image'),
                'required' => false,
                'note' => 'Allow image type: jpg, gif, jpeg, png </br> Best image ratio is 1200x400px',
            )
        );

        $fieldset->addField(
            'country',
            'select',
            array(
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $countries,
                'required' => false,
            )
        );
        $fieldset->addField(
            'state_province',
            'select',
            array(
                'name' => 'state_province',
                'label' => __('State/Province'),
                'title' => __('State/Province'),
                'required' => false,
                'style' => 'width: 50%;',
                'values' => $this->_getStateProvince()
            )
        );

        $fieldset->addField(
            'city',
            'text',
            array(
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City')
            )
        );

        $fieldset->addField(
            'zip',
            'text',
            array(
                'name' => 'zip',
                'label' => __('Zip'),
                'title' => __('Zip')
            )
        );
        $fieldset->addField(
            'address',
            'customaddress',
            array(
                'name' => 'address',
                'label' => __('Address'),
                'title' => __('address'),
                'note' => '<font color="red">Google Api Map free until exceeding 25,000 map loads per 24 hours</font></br><a target="_blank" href="https://developers.google.com/maps/documentation/javascript/usage-and-billing">learn more</a>'
            )
        );


        $fieldset->addField(
            'website',
            'text',
            array(
                'name' => 'website',
                'label' => __('Website'),
                'title' => __('website'),
                'class' => 'validate-url',
                'note' => __('Input your Website with http/https'),
            )
        );

        $fieldset->addField(
            'email',
            'text',
            array(
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('email')
            )
        );

        $fieldset->addField(
            'phone_number',
            'text',
            array(
                'name' => 'phone_number',
                'label' => __('Phone Number'),
                'title' => __('phone_number')
            )
        );

        $fieldset->addField(
            'latitude',
            'text',
            array(
                'name' => 'latitude',
                'label' => __('Latitude'),
                'title' => __('latitude'),
                'required' => true,
            )
        );

        $fieldset->addField(
            'longitude',
            'text',
            array(
                'name' => 'longitude',
                'label' => __('Longitude'),
                'title' => __('longitude'),
                'required' => true,
            )
        );
        $fieldset->addField(
            'map',
            'map',
            array(
                'name' => 'map',
                'label' => __('Map'),
                'title' => __('Map')
            )
        );

        $fieldset->addField(
            'small_image',
            'customicon',
            array(
                'name' => 'small_image',
                'label' => __('Store Icon'),
                'title' => __('Store Icon'),
                'required' => false,
                'note' => 'Allow image type: jpg, gif, jpeg, png',
            )
        );

        $fieldset->addField(
            'store',
            'multiselect',
            array(
                'name' => 'store',
                'label' => __('Assign to Store Views'),
                'title' => __('Assign to Store Views'),
                'required' => true,
                'values' => $this->_store->getStoreValuesForForm(false, true)
            )
        );



        $form->setValues($data);
        $this->setForm($form);


        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return __('Store Settings');
    }

    public function getTabTitle()
    {
        return __('Store Settings');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }



    private function _getAllProduct()
    {
        // Get our collection
        $existingProduct = $this->_productFactory->create()->getCollection()->getData();
        $productList = array();
        foreach ($existingProduct as $product) {
            $productList[] = array(
                'value' => $product['entity_id'],
                'label' => $product['sku']
            );
        }

        return $productList;
    }

    private function _getStateProvince()
    {
        // Get our collection

        $model = $this->_coreRegistry->registry('maplist_location_edit');
        $data = $model->getData();
        $regionData = array();
        if ($data) {
            $regions = $this->regionColFactory->create()->getCollection()->addFieldToFilter('country_id', $data['country']);
            foreach ($regions as $region) {
                $regionData[] = array(
                    'value' => $region['code'],
                    'label' => $region['name']
                );
            }
        }

        return $regionData;
    }
}
