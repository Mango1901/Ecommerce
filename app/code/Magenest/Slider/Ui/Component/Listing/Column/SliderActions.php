<?php

namespace Magenest\Slider\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class SliderActions extends Column
{
    /** @var UrlInterface  */
    protected $urlBuilder;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = array(),
        array $data = array()
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['edit'] = array(
                    'href' => $this->urlBuilder->getUrl(
                        'slider/slider/edit',
                        array('id' => $item['slider_id'])
                    ),
                    'label' => __('Edit'),
                    'hidden' => false,
                );
                $item[$this->getData('name')]['delete'] = array(
                    'href' => $this->urlBuilder->getUrl(
                        'slider/slider/delete',
                        array('id' => $item['slider_id'])
                    ),
                    'label' => __('Delete'),
                    'hidden' => false,
                    'confirm' => [
                        'title' => __('Delete "${ $.$data.name }"'),
                        'message' => __('Are you sure you wan\'t to delete "${ $.$data.name }" slider?')
                    ]
                );
            }
        }

        return $dataSource;
    }
}