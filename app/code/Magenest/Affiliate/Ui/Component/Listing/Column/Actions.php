<?php


namespace Magenest\Affiliate\Ui\Component\Listing\Column;

use Magenest\Affiliate\Model\Withdraw\Status;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 * @package Magenest\Affiliate\Ui\Component\Listing\Column
 */
class Actions extends Column
{
    /**
     * URL builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Actions constructor.
     *
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $config = $this->getData('config');
            foreach ($dataSource['data']['items'] as & $item) {
                $indexField = $config['indexField'];
                if (isset($item[$indexField])) {
                    foreach ($config['action_list'] as $name => $action) {
                        $actionArray = [
                            'href' => $this->_urlBuilder->getUrl(
                                $action['url_path'],
                                [$config['paramName'] => $item[$indexField]]
                            ),
                            'label' => __($action['label'])
                        ];
                        if ($name == 'delete') {
                            $actionArray['confirm'] = [
                                'title' => __('Delete "%1"', $item[$indexField]),
                                'message' => __('Are you sure?')
                            ];
                        }

                        if ($name == 'retry' && $item['status'] != Status::FAILED) {
                            continue;
                        }

                        $item[$this->getData('name')][$name] = $actionArray;
                    }
                }
            }
        }

        return $dataSource;
    }
}
