<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 21/09/2016
 * Time: 15:01
 */

namespace Magenest\MapList\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\MapList\Model\LocationFactory;

/**
 * Class Logo
 *
 * @package Magenest\MapList\Ui\Component\Listing\Columns
 */
class Image extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_locationFactory;

    protected $_serializer;

    /**
     * Icon constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storeManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        LocationFactory $locationFactory,
        \Magento\Framework\Serialize\SerializerInterface $serializer,
        array $components = array(),
        array $data = array()
    ) {
        $this->_serializer      = $serializer;
        $this->_storeManager    = $storeManager;
        $this->urlBuilder       = $urlBuilder;
        $this->_locationFactory = $locationFactory;
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
        $locationModel  = $this->_locationFactory->create();
        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            $i         = 0;
            foreach ($dataSource['data']['items'] as & $item) {
                $locationId = $item['location_id'];
                $location   = $locationModel->load($locationId);
                $image      = $this->_serializer->unserialize($location->getData('big_image'));

                $template = new \Magento\Framework\DataObject($item);
                if (!!$image) {
                    $imageUrl = $mediaDirectory . 'maplist/location/image' . $image['file'];
                } else {
                    $imageUrl = '//';
                }

                $item[$fieldName . '_src']       = $imageUrl;
                $item[$fieldName . '_alt']       = $template->getName();
                $item[$fieldName . '_orig_src']  = $imageUrl;
                $dataSource['data']['items'][$i] = $item;
                $i++;
            }
        }

        return $dataSource;
    }
}
