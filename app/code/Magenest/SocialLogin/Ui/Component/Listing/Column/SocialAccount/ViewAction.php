<?php

namespace Magenest\SocialLogin\Ui\Component\Listing\Column\SocialAccount;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ViewAction
 * @package Magenest\SocialLogin\Ui\Component\Listing\Column\SocialAccount
 */
class ViewAction extends Column
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * ViewAction constructor.
     * @param \Magento\Framework\UrlInterface $urlBuider
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuider,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ){
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->_urlBuilder = $urlBuider;
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if(isset($dataSource['data']['items'])){
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['view'] = [
                    'href' => $this->_urlBuilder->getUrl(
                        'customer/index/edit',
                        ['id' => $item['customer_id']]
                    ),
                    'label' => __('View Customer'),
                    'hidden' => false,
                ];
            }
        }
        return $dataSource;
    }
}