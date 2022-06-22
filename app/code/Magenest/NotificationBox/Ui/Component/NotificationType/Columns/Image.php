<?php

namespace Magenest\NotificationBox\Ui\Component\NotificationType\Columns;

use Exception;
use Magenest\NotificationBox\Model\NotificationType;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Image
 * @package Magenest\NotificationBox\Ui\Component\Listing\Columns
 */
class Image extends Column
{
    const NAME = 'image';

    const ALT_FIELD = 'name';

    /** @var \Magento\Catalog\Helper\Image */
    protected $imageHelper;

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var Json */
    protected $serialize;

    /**
     * @param Json $serialize
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        Json                          $serialize,
        ContextInterface              $context,
        UiComponentFactory            $uiComponentFactory,
        \Magento\Catalog\Helper\Image $imageHelper,
        UrlInterface                  $urlBuilder,
        array                         $components = [],
        array                         $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->imageHelper = $imageHelper;
        $this->urlBuilder = $urlBuilder;
        $this->serialize = $serialize;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['icon'])) {
                    try {
                        $item['icon'] = $this->serialize->unserialize($item['icon']);
                    } catch (Exception $e) {
                        $item['icon'] = '';
                    }
                    $item[$fieldName . '_src'] = isset($item['icon'][0]['url']) ? $item['icon'][0]['url'] : '';
                    $item[$fieldName . '_orig_src'] = isset($item['icon'][0]['url']) ? $item['icon'][0]['url'] : '';
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'notibox/notificationtype/newAction', ['entity_id' => $item['entity_id']]
                    );
                }
                if (isset($item['is_category'])) {
                    if ($item['is_category'] == NotificationType::IS_CATEGORY) {
                        $item['is_category'] = __('Yes');
                    } else {
                        $item['is_category'] = __('No');
                    }
                }
            }
        }
        return $dataSource;
    }
}
