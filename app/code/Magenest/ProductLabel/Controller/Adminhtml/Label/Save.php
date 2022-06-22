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

namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Exception;
use Magenest\ProductLabel\Api\Data\LabelInterface;
use Magenest\ProductLabel\Controller\Adminhtml\Label;

/**
 * Class Save
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class Save extends Label
{
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $dataPost = $this->getRequest()->getPostValue();
        if ($dataPost) {
            $data = $this->processData($dataPost);
            $categoryData = isset($data['category_data']) ? $data['category_data'] : '';
            $productData = isset($data['product_data']) ? $data['product_data'] : '';

            //Add label size default
            $data['category_data']['label_size'] = LabelInterface::LABEL_SIZE_DEFAULT;

            $data['product_data']['label_size'] = LabelInterface::LABEL_SIZE_DEFAULT;

            //Add position default
            $data['category_data']['position'] = LabelInterface::POSITION_DEFAULT;

            $data['product_data']['position'] = LabelInterface::POSITION_DEFAULT;

            //Add text size default
            $data['category_data']['text_size'] = isset($categoryData['text_size']) && $categoryData['text_size'] == ''
                ? LabelInterface::TEXT_SIZE_DEFAULT
                : $categoryData['text_size'];

            $data['product_data']['text_size'] = isset($productData['text_size']) && $productData['text_size'] == ''
                ? LabelInterface::TEXT_SIZE_DEFAULT
                : $productData['text_size'];

            $id = isset($data['label_id']) ? $data['label_id'] : '';
            $conditions = isset($dataPost['conditions']) ? $dataPost['conditions'] : '';
            $dateProcessing = $this->dateProcessing($conditions);
            $dataPost['conditions'] = $dateProcessing;
            try {
                $model = $id ? $this->labelRepository->get($id) : $this->labelRepository->createNewObject()->load($id);
                $data = array_filter(
                    $data, function ($value) {
                    return $value != '';
                });
                $model->loadPost($data);
                $fromDate = isset($dataPost['conditions']['from_date']) ? $dataPost['conditions']['from_date'] : '';
                $toDate = isset($dataPost['conditions']['to_date']) ? $dataPost['conditions']['to_date'] : '';
                $model->setData('from_date', $fromDate);
                $model->setData('to_date', $toDate);
                $this->_eventManager->dispatch('before_save_product_label', ['product_label' => $model]);
                $this->labelRepository->save($model);
                $this->messageManager->addSuccessMessage('Save product label successfully!');
                $this->session->setFromData(false);
                if ($this->getRequest()->getParam('back') == 'edit') {
                    $this->_redirect('*/*/edit', ['label_id' => $model->getId(), '_current' => true]);
                    return;
                }

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the item data.')
                );
                $this->session->setFormData($data);
                $this->logger->critical($e);

                return $resultRedirect->setPath('*/*/');
            }
        }
    }

    /**
     * @param $dataPost
     * @return mixed
     */
    public function processData($dataPost)
    {
        $data = isset($dataPost['general']) ? $dataPost['general'] : '';
        $data['name'] = isset($data['name']) ? trim($data['name']) : '';
        $data['category_data'] = isset($dataPost['category']) ? $dataPost['category'] : '';
        $data['product_data'] = isset($dataPost['product']) ? $dataPost['product'] : '';

        if (isset($dataPost['conditions'])) {
            $data['from_date'] = isset($dataPost['conditions']['from_date']) ? $dataPost['conditions']['from_date'] : '';
            $data['to_date'] = isset($dataPost['conditions']['to_date']) ? $dataPost['conditions']['to_date'] : '';
            $data['label_type'] = isset($dataPost['conditions']['label_type']) ? $dataPost['conditions']['label_type'] : '';
        }
        if (isset($dataPost['rule'])) {
            $data['rule'] = $dataPost['rule'];
        }
        $data['form_key'] = $dataPost['form_key'];
        if (isset($data['category_data']['image']) && isset($data['category_data']['image'][0]['name'])) {
            $data['category_data']['image'] = $data['category_data']['image'][0]['name'];
        }
        if (isset($data['product_data']['image']) && isset($data['product_data']['image'][0]['name'])) {
            $data['product_data']['image'] = $data['product_data']['image'][0]['name'];
        }
        if (isset($data['category_data']['type'])) {
            switch ($data['category_data']['type']) {
                case 1:
                    $data['category_data']['shape_type'] = null;
                    $data['category_data']['shape_color'] = null;
                    $data['category_data']['image'] = null;
                    break;
                case 2:
                    $data['category_data']['image'] = null;
                    break;
                case 3:
                    $data['category_data']['shape_type'] = null;
                    $data['category_data']['shape_color'] = null;
                    $data['category_data']['text'] = null;

            }
        }
        if (isset($data['product_data']['type'])) {
            switch ($data['product_data']['type']) {
                case 1:
                    $data['product_data']['shape_type'] = null;
                    $data['product_ata']['shape_color'] = null;
                    $data['product_data']['image'] = null;
                    break;
                case 2:
                    $data['product_data']['image'] = null;
                    break;
                case 3:
                    $data['product_data']['shape_type'] = null;
                    $data['product_data']['shape_color'] = null;
                    $data['product_data']['text'] = null;
            }
        }
        if (isset($data['rule'])) {
            $data['conditions'] = $data['rule']['conditions'];
            unset($data['rule']);
        }

        if (isset($data['priority']) && $data['priority'] == '') {
            $data['priority'] = '0';
        }

        if (isset($data['product_data']['use_default']) && $data['product_data']['use_default'] == 1) {
            if (isset($data['product_data']['option_id'])) {
                $optionId = $data['product_data']['option_id'];
                $data['product_data'] = $data['category_data'];
                $data['product_data']['option_id'] = $optionId;
            } else {
                $data['product_data'] = $data['category_data'];
            }
            $data['product_data']['use_default'] = 1;
        }

        return $data;
    }

    /**
     * @param $conditions
     * @return mixed
     */
    public function dateProcessing($conditions)
    {
        if ($conditions != '' && !$conditions['date_range']) {
            $conditions['from_date'] = '';
            $conditions['to_date'] = '';
        }
        unset($conditions['date_range']);
        return $conditions;
    }
}
