<?php
namespace Magenest\NotificationBox\Ui\Component\Listing\Columns;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ConvertTimeZone extends Column
{
    /**
     * URL builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /** @var TimezoneInterface  */
    protected $timezoneInterface;

    /**
     * constructor
     *
     * @param TimezoneInterface $timezoneInterface
     * @param UrlInterface $urlBuilder
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        TimezoneInterface $timezoneInterface,
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->timezoneInterface = $timezoneInterface;
        $this->_urlBuilder = $urlBuilder;
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
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['created_at'])) {
                    $createdAt = $this->timezoneInterface->formatDateTime($item['created_at'],2,2);
                    $item['created_at'] = $createdAt;
                }
            }
        }
        return $dataSource;
    }
}
