<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Block\Adminhtml\Label\Edit\Tab;

/**
 * Class Preview
 * @package Magenest\ProductLabel\Block\Adminhtml\Label\Edit\Tab
 */
class Preview extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'Magenest_ProductLabel::label_preview.phtml';

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    private $_formFactory;

    /**
     * Preview constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = [])
    {
        $this->registry = $registry;
        $this->_formFactory = $formFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Phrase|string
     */
    public function getTabLabel()
    {
        return __('Label Preview');
    }

    /**
     * Return Tab title
     *
     * @return string
     * @api
     */
    public function getTabTitle()
    {
        return __('Label Preview');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     * @api
     */
    public function isHidden()
    {
        return false;
    }

    public function isAjaxLoaded()
    {
        return false;
    }

    /**
     * Init data for label
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLabelData()
    {
        $item = $this->registry->registry('magenest_product_label');
        $data = $item->getData();
        if ($data) {
            $data['product_data']['image_url'] = $this->getImgUrl($data['product_data']['image']);
            $data['category_data']['image_url'] = $this->getImgUrl($data['category_data']['image']);

        } else {
            $data = [
                'category_data' => [
                    'position' => 'top-left',
                    'type' => '1',
                    'text' => '',
                    'text_font' => '',
                    'text_size' => '16',
                    'text_color' => '#000000',
                    'shape_type' => '',
                    'shape_color' => '#000000',
                    'image' => '',
                    'image_url' => '',
                    'label_size' => '80',
                    'custom_css' => ''
                ]
            ];
            $data['product_data'] = $data['category_data'];
            $data['product_data']['use_default'] = 0;
        }

        return $data;

    }

    /**
     * @param $img
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getImgUrl($img) {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'label/tmp/image/' . $img;
    }
}
