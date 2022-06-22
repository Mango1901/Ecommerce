<?php
/**
 * Created by PhpStorm.
 * User: duccanh
 * Date: 23/05/2016
 * Time: 00:51
 */

namespace Magenest\MapList\Ui\Component\Listing\Columns;

use Magenest\MapList\Model\Config\Source\Router;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Logo
 *
 * @package Magenest\ShopByBrand\Ui\Component\Listing\Columns
 */
class Logo extends Column
{
    /**
     * Url path
     */
    const BLOG_URL_PATH_EDIT = 'maplist/brand/edit';
    const BLOG_URL_PATH_DELETE = 'maplist/brand/delete';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var string
     */
    private $editUrl;

    protected $_repository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param StoreManagerInterface $storemanager
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storemanager,
        \Magento\Framework\View\Asset\Repository $repository,
        array $components = array(),
        array $data = array(),
        $editUrl = self::BLOG_URL_PATH_EDIT
    ) {
        $this->_storeManager = $storemanager;
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        $this->_repository = $repository;
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

        $mediaDirectory = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        );

        $imgdefault = $this->_repository->getUrl("Magento_Catalog::images/product/placeholder/thumbnail.jpg");

        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $template = new \Magento\Framework\DataObject($item);
                $imageUrl = $mediaDirectory . Router::ROUTER_MEDIA . $template->getLogo();
                if ($template->getLogo()) {
                    $item[$fieldName . '_src'] = $imageUrl;
                } else {
                    $item[$fieldName . '_src'] = $imgdefault;
                }

                $item[$fieldName . '_alt'] = $template->getName();
                $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                    'maplist/brand/edit',
                    array(
                        'brand_id' => $item['brand_id'],
                    )
                );

                if ($template->getLogo()) {
                    $item[$fieldName . '_orig_src'] = $imageUrl;
                } else {
                    $item[$fieldName . '_orig_src'] = $imgdefault;
                }
            }
        }

        return $dataSource;
    }
}
