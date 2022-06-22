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

namespace Magenest\ProductLabel\Model\Label\Source;

use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{
    const DATE_RANGE_ENABLE = '1';

    const DATE_RANGE_DISABLE = '0';

    /**
     * @var array
     */
    protected $_loadedData;

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magenest\ProductLabel\Api\LabelRepositoryInterface $labelRepository,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $meta = [],
        array $data = []
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->collection = $labelRepository->createCollection();
        $this->_storeManager = $storeManager;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->_loadedData)) {
            return $this->_loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $item) {
            $this->_loadedData[$item->getLabelId()] = $item->getData();
        }
        $data = $this->dataPersistor->get('magenest_product_label');
        if (!empty($data)) {

            $formData['category'] = $data['category_data'];
            $formData['product'] = $data['product_data'];
            unset($data['category_data']);
            unset($data['product_data']);
            $formData['general'] = $data;
            $formData['conditions'] = [
                'from_date' => isset($data['from_date']) ? $data['from_date'] : '',
                'to_date' => isset($data['to_date']) ? $data['to_date'] : '',
                'label_type' => isset($data['label_type']) ? $data['label_type'] : '',
            ];
            if(isset($formData['category']['image'])) {
                $image = $formData['category']['image'];
                unset($formData['category']['image']);
                $formData['category']['image'][0]['name'] = $image;
                $formData['category']['image'][0]['url'] = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'label/tmp/image/' . $image;
            }
            if(isset($formData['product']['image'])) {
                $image = $formData['product']['image'];
                unset($formData['product']['image']);
                $formData['product']['image'][0]['name'] = $image;
                $formData['product']['image'][0]['url'] = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'label/tmp/image/' . $image;
            }
            //Check show date range
            $conditions = isset($formData['conditions']) ? $formData['conditions'] : '';
            if ($conditions != '' && $conditions['from_date'] || $conditions['to_date']) {
                $formData['conditions']['date_range'] = self::DATE_RANGE_ENABLE;
            } else {
                $formData['conditions']['date_range'] = self::DATE_RANGE_DISABLE;
            }
            $item = $this->collection->getNewEmptyItem();
            $item->setData($formData);
            $this->_loadedData[$item->getData('general')['label_id']] = $item->getData();
            $this->dataPersistor->clear('magenest_product_label');

        }

        return $this->_loadedData;
    }
}
