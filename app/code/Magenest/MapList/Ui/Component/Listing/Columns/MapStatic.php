<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/28/16
 * Time: 15:55
 */

namespace Magenest\MapList\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Logo
 *
 * @package Magenest\MapList\Ui\Component\Listing\Columns
 */
class MapStatic extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Icon constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */

    protected $_scopeConfig;

    private $zoomLevel = 15;
    private $size = 500;
    private $mapType = 'roadmap';
    private $markerColor = 'red';
    private $mapUrl = 'https://maps.googleapis.com/maps/api/staticmap?';
    private $markerLabel = "";
    private $apiKey;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface,
        array $components = array(),
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfigInterface;
        $this->apiKey = $this->_scopeConfig->getValue('maplist/map/api');
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param  array $dataSource
     * @return array
     */

    private function getStaticUrl($lat, $lng)
    {
        return $this->mapUrl . "center=" . $lat . "," . $lng . "&zoom=" . $this->zoomLevel
            . "&size=" . $this->size . "x" . $this->size . "&maptype=" . $this->mapType
            . "&markers=color:" . $this->markerColor . "%7Clabel:" . $this->markerLabel . "%7C"
            . $lat . "," . $lng . "&key=" . $this->apiKey;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $address = $this->getStaticUrl($item['latitude'], $item['longitude']);
                $item[$fieldName . '_src'] = $address;
                $item[$fieldName . '_alt'] = $item['title'];
                $item[$fieldName . '_orig_src'] = $address;
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'maplist/location/edit',
                    ['id' => $item['location_id'], 'store' => $this->context->getRequestParam('store')]
                );
            }
        }

        return $dataSource;
    }
}
