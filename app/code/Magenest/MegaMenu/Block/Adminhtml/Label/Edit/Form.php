<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\MegaMenu\Block\Adminhtml\Label\Edit;

use Magento\Backend\Block\Widget\Form\Generic;

class Form extends Generic
{
    protected $_options;

    protected $_wysiwygConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magenest\MegaMenu\Model\Label $labelModel,
        array $data = []
    ) {
        $this->_options = $labelModel;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareData($model)
    {
        $data = [];
        $html = $model->getToHtml();
        $labelText = $model->getLabelText();
        try {
            $xmlElement = simplexml_load_string($html);
        } catch (\Exception $e) {
            // Catch exception while parsing html:
            //  If label text contains non-close tag(s), exception occurs
            //  => Solution: replace label text by a dummy text that doesn't contain non-close tags
            //  But sometimes replacing doesn't work correctly
            //  Example: $labelText = <label id="megamenu-label" class="megamenu-label label-position-top">
            $html = str_replace($labelText, "dummy text", $html);
            $xmlElement = simplexml_load_string($html);
        }
        $attributes = json_decode(json_encode($xmlElement), true);
        foreach ($attributes['@attributes'] as $key => $attribute) {
            if ($key == 'class') {
                $data['class'] = trim(trim($attribute, 'megamenu-label '), 'arrow');
                if (strpos($attribute, 'arrow') !== false) {
                    $data['arrow'] = 1;
                } else {
                    $data['arrow'] = 0;
                }
            } elseif ($key == 'style') {
                $attrs = explode(';', $attribute);
                foreach ($attrs as $k => $attr) {
                    $items = explode(': ', $attr);
                    if (strpos($items[0], 'color') !== false) {
                        $rgb = explode(',', $items[1]);
                        $items[1] = $this->rgb2html(preg_replace('/[^0-9]/', '', $rgb[0]), preg_replace('/[^0-9]/', '', $rgb[1]), preg_replace('/[^0-9]/', '', $rgb[2]));
                        $data[trim($items[0])] = $items[1];
                    } else {
                        if (isset($items[1])) {
                            $data[trim($items[0])] = trim($items[1], 'px');
                        }
                    }
                }
            }
        }
        if (isset($attributes['span'])) {
            $data['text'] = $labelText;
            foreach ($attributes['span'][1]['@attributes'] as $key => $attribute) {
                if ($key == 'style') {
                    $attrs = explode(';', $attribute);
                    foreach ($attrs as $k => $attr) {
                        $items = explode(': ', $attr);
                        if (strpos($items[0], 'color') !== false) {
                            $rgb = explode(',', $items[1]);
                            $items[1] = $this->rgb2html(preg_replace('/[^0-9]/', '', $rgb[0]), preg_replace('/[^0-9]/', '', $rgb[1]), preg_replace('/[^0-9]/', '', $rgb[2]));
                            $data['arrow-' . trim($items[0])] = $items[1];
                        } else {
                            if (isset($items[1])) {
                                $data['arrow-' . trim($items[0])] = trim($items[1], 'px');
                            }
                        }
                    }
                }
            }
        }
        $data['position'] = $model->getLabelPosition();

        return $data;
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('magenest_menu_label');
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]]
        );

        $form->setHtmlIdPrefix('mm_label_');
        if ($model->getLabelId()) {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Edit Row Data'), 'class' => 'fieldset-wide', 'data-bind' => "scope: 'label_preview'"]
            );
            $fieldset->addField('label_id', 'hidden', ['name' => 'label_id']);
            $model->addData($this->_prepareData($model));
        } else {
            $fieldset = $form->addFieldset(
                'base_fieldset',
                ['legend' => __('Add Row Data'), 'class' => 'fieldset-wide']
            );
        }

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Title'),
                'id' => 'label-title',
                'title' => __('Title'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'text',
            'text',
            [
                'name' => 'label_text',
                'label' => __('Text'),
                'id' => 'label-text',
                'title' => __('Text'),
                'class' => 'required-entry',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'position',
            'select',
            [
                'name' => 'label_position',
                'label' => __('Position'),
                'id' => 'label-position',
                'title' => __('Position'),
                'class' => 'required-entry',
                'required' => true,
                'options' => [
                    'label-position-top' => 'Top',
                    'label-position-left' => 'Left',
                    'label-position-right' => 'Right',
                    'label-position-top-left' => 'Top Left',
                    'label-position-top-right' => 'Top Right'
                ]
            ]
        );

        $fieldset->addField(
            'font-size',
            'text',
            [
                'name' => 'label_font',
                'label' => __('Font Size'),
                'id' => 'label-font',
                'title' => __('Font Size'),
                'note' => __('Enter number. (Default: 9. Unit: pixel)'),
                'class' => 'validate-not-negative-number'
            ]
        )->setType('number');

        $fieldset->addField(
            'width',
            'text',
            [
                'name' => 'label_width',
                'label' => __('Width'),
                'id' => 'label-width',
                'title' => __('Width'),
                'note' => __('Enter number. (Default: auto. Unit: pixel)'),
                'class' => 'validate-not-negative-number'
            ]
        )->setType('number');

        $fieldset->addField(
            'height',
            'text',
            [
                'name' => 'label_height',
                'label' => __('Height'),
                'id' => 'label-height',
                'title' => __('Height'),
                'note' => __('Enter number. (Default: auto. Unit: pixel)'),
                'class' => 'validate-not-negative-number'
            ]
        )->setType('number');

        $fieldset->addField(
            'text-align',
            'select',
            [
                'name' => 'label_text_align',
                'label' => __('Text Align'),
                'id' => 'label-text-align',
                'title' => __('Text Align'),
                'options' => ['center' => 'Center', 'left' => 'Left', 'right' => 'Right'],
            ]
        );

        $fieldset->addField(
            'color',
            'text',
            [
                'name' => 'label_text_color',
                'label' => __('Text Color'),
                'id' => 'label-text-color',
                'title' => __('Text Color'),
                'note' => __('Enter color code. (Default: #000)'),
            ]
        );

        $fieldset->addField(
            'background-color',
            'text',
            [
                'name' => 'label_background_color',
                'label' => __('Background Color'),
                'id' => 'label-background-color',
                'title' => __('Background Color'),
            ]
        );

        $fieldset->addField(
            'border-width',
            'text',
            [
                'name' => 'label_border_width',
                'label' => __('Border Width'),
                'id' => 'label-border-width',
                'title' => __('Border Width'),
                'note' => __('Enter number. (Unit: pixel)'),
                'class' => 'validate-not-negative-number'
            ]
        )->setType('number');

        $fieldset->addField(
            'border-style',
            'select',
            [
                'name' => 'label_border_style',
                'label' => __('Border Style'),
                'id' => 'label-border-style',
                'title' => __('Border Style'),
                'options' => ['none' => 'None', 'dashed' => 'Dashed', 'dotted' => 'Dotted', 'double' => 'Double', 'solid' => 'Solid']
            ]
        );

        $fieldset->addField(
            'border-color',
            'text',
            [
                'name' => 'label_border_color',
                'label' => __('Border Color'),
                'id' => 'label-border-color',
                'title' => __('Border Color'),
            ]
        );

        $fieldset->addField(
            'border-radius',
            'text',
            [
                'name' => 'label_border_radius',
                'label' => __('Border Radius'),
                'id' => 'label-border-radius',
                'title' => __('Border Radius'),
                'note' => __('Enter number. (Unit: pixel)'),
                'class' => 'validate-not-negative-number'
            ]
        )->setType('number');

        $fieldset->addField(
            'arrow',
            'select',
            [
                'name' => 'label_arrow',
                'label' => __('Enable Arrow'),
                'id' => 'label-arrow',
                'title' => __('Enable Arrow'),
                'class' => 'required-entry',
                'required' => true,
                'options' => ['0' => 'Disable', '1' => 'Enable'],
            ]
        );

        $fieldset->addField(
            'arrow-border-width',
            'text',
            [
                'name' => 'label_arrow_width',
                'label' => __('Arrow Width'),
                'id' => 'label-arrow-width',
                'title' => __('Arrow Width'),
                'note' => __('Enter number. (Default: 3. Unit: pixel)'),
                'class' => 'validate-not-negative-number'
            ]
        )->setType('number');

        $fieldset->addField(
            'arrow-border-color',
            'text',
            [
                'name' => 'label_arrow_color',
                'label' => __('Arrow Color'),
                'id' => 'label-arrow-color',
                'title' => __('Arrow Color'),
            ]
        );
        $fieldset->addField(
            'to_html',
            'hidden',
            [
                'name' => 'to_html',
                'label' => __('to_html'),
                'id' => 'to_html',
                'title' => __('to_html'),
            ]
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function rgb2html($r, $g = -1, $b = -1)
    {
        if (is_array($r) && count($r) === 3) {
            list($r, $g, $b) = $r;
        }

        $r = intval($r);
        $g = intval($g);
        $b = intval($b);

        $r = dechex($r < 0 ? 0 : ($r > 255 ? 255 : $r));
        $g = dechex($g < 0 ? 0 : ($g > 255 ? 255 : $g));
        $b = dechex($b < 0 ? 0 : ($b > 255 ? 255 : $b));

        $color = (strlen($r) < 2 ? '0' : '') . $r;
        $color .= (strlen($g) < 2 ? '0' : '') . $g;
        $color .= (strlen($b) < 2 ? '0' : '') . $b;

        return '#' . $color;
    }
}
