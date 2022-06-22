<?php
namespace Magenest\FastErp\Ui\DataProvider;

use Magenest\FastErp\Model\ResourceModel\ErpHistoryLog\CollectionFactory;
use Magento\Framework\UrlInterface;

class ErpLog extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var UrlInterface */
    private $urlBuilder;

    /**
     * @param CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        UrlInterface      $urlBuilder,
        string            $name,
        string            $primaryFieldName,
        string            $requestFieldName,
        array             $meta = [],
        array             $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        foreach ($data['items'] as &$record) {
            if ($record['order_id']) {
                $record['order_url'] = $this->urlBuilder->getUrl(
                    'sales/order/view',
                    ['order_id' => $record['order_id']]
                );
            }

        }

        return $data;
    }
}
