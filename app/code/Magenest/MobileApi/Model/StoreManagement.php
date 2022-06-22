<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model;

use Magenest\MobileApi\Model\Catalog\Widget\ProductSlider;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObjectFactory;
use Magenest\MobileApi\Api\StoreManagementInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection\Factory as CategoryCollectionFactory;
use Magento\Catalog\Model\Category\TreeFactory;
use Magento\Catalog\Model\CategoryManagementFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Customer\Model\Session as CustomerSession;
use Magenest\MobileApi\Model\Catalog\Widget\ProductFactory as ProductWidgetFactory;
use Magenest\MobileApi\Model\Catalog\Widget\NewProductFactory as NewProductWidgetFactory;
use Magenest\MobileApi\Api\Data\Store\HomeInterfaceFactory;
use Magenest\MobileApi\Api\Data\Store\ContactInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\View\Element\BlockFactory;
use Magenest\MegaMenu\Block\Menu\Entity;
use Magento\Framework\App\Area;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\MobileApi\Api\Data\Store\PromotionInterfaceFactory;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\UrlInterface;
use Magenest\MobileApi\Api\Data\Store\MediaEntryInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magenest\MobileApi\Model\Helper as ApiHelper;
use Magento\Catalog\Model\ResourceModel\Category as CategoryResource;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Magenest\MobileApi\Model\Store\Home\StoreDetector;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magenest\MobileApi\Setup\Patch\Data\HomeBannerSlider;

/**
 * Class StoreManagement
 * @package Magenest\MobileApi\Model
 */
class StoreManagement implements StoreManagementInterface
{
    /** Const */
    const MOBILE_APP_TOKEN_PATH = 'mobile/static/general/apptoken';
    const MOBILE_APP_LICENSES_PATH = 'mobile/static/general/license';

    /**
     * @var array
     */
    protected $_categories = [];

    /**
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var CategoryCollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var TreeFactory
     */
    protected $_treeFactory;

    /**
     * @var CategoryManagementFactory
     */
    protected $_categoryManagementFactory;

    /**
     * @var CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var ProductStatus
     */
    protected $_productStatus;

    /**
     * @var ProductVisibility
     */
    protected $_productVisibility;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var ProductWidgetFactory
     */
    protected $_productWidgetFactory;

    /**
     * @var NewProductWidgetFactory
     */
    protected $_newProductWidgetFactory;

    /**
     * @var HomeInterfaceFactory
     */
    protected $_homeFactory;

    /**
     * @var MailInterface
     */
    protected $_contactMail;

    /**
     * @var DataPersistorInterface
     */
    protected $_dataPersistor;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Emulation
     */
    protected $_appEmulation;

    /**
     * @var PromotionInterfaceFactory
     */
    protected $_promotionFactory;

    /**
     * @var AssetRepository
     */
    protected $_assetRepo;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var ReadInterface
     */
    protected $_mediaDirectory;

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var ApiHelper
     */
    protected $_apiHelper;

    /**
     * @var IoFile
     */
    protected $_ioFile;

    /**
     * @var StoreDetector
     */
    protected $_storeDetector;

    /**
     * @var CategoryResource
     */
    protected $_categoryResource;

    /** @var Json */
    protected $jsonHelper;

    /** @var CmsCollection */
    protected $blockCollectionFactory;

    /**
     * Constructor.
     *
     * @param DataObjectFactory $dataObjectFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param TreeFactory $treeFactory
     * @param CategoryManagementFactory $categoryManagementFactory
     * @param CategoryFactory $categoryFactory
     * @param ProductStatus $productStatus
     * @param ProductVisibility $productVisibility
     * @param CustomerSession $customerSession
     * @param ProductWidgetFactory $productWidgetFactory
     * @param HomeInterfaceFactory $homeInterfaceFactory
     * @param NewProductWidgetFactory $newProductFactory
     * @param MailInterface $contactMail
     * @param StoreManagerInterface $storeManager
     * @param Emulation $emulation
     * @param DataPersistorInterface $dataPersistor
     * @param PromotionInterfaceFactory $promotionInterfaceFactory
     * @param AssetRepository $assetRepository
     * @param UrlInterface $urlInterface
     * @param Filesystem $filesystem
     * @param ApiHelper $apiHelper
     * @param IoFile $ioFile
     * @param StoreDetector $storeDetector
     * @param CategoryResource $categoryResource
     * @param Json $jsonHelper
     * @param CmsCollection $blockCollectionFactory
     */
    function __construct(
        DataObjectFactory $dataObjectFactory,
        ScopeConfigInterface $scopeConfig,
        CategoryCollectionFactory $categoryCollectionFactory,
        TreeFactory $treeFactory,
        CategoryManagementFactory $categoryManagementFactory,
        CategoryFactory $categoryFactory,
        ProductStatus $productStatus,
        ProductVisibility $productVisibility,
        CustomerSession $customerSession,
        ProductWidgetFactory $productWidgetFactory,
        HomeInterfaceFactory $homeInterfaceFactory,
        NewProductWidgetFactory $newProductFactory,
        MailInterface $contactMail,
        StoreManagerInterface $storeManager,
        Emulation $emulation,
        DataPersistorInterface $dataPersistor,
        PromotionInterfaceFactory $promotionInterfaceFactory,
        AssetRepository $assetRepository,
        UrlInterface $urlInterface,
        Filesystem $filesystem,
        ApiHelper $apiHelper,
        IoFile $ioFile,
        StoreDetector $storeDetector,
        CategoryResource $categoryResource,
        Json $jsonHelper,
        CmsCollection $blockCollectionFactory
    )
    {
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_treeFactory = $treeFactory;
        $this->_categoryManagementFactory = $categoryManagementFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_productStatus = $productStatus;
        $this->_productVisibility = $productVisibility;
        $this->_customerSession = $customerSession;
        $this->_productWidgetFactory = $productWidgetFactory;
        $this->_homeFactory = $homeInterfaceFactory;
        $this->_newProductWidgetFactory = $newProductFactory;
        $this->_contactMail = $contactMail;
        $this->_dataPersistor = $dataPersistor;
        $this->_storeManager = $storeManager;
        $this->_appEmulation = $emulation;
        $this->_promotionFactory = $promotionInterfaceFactory;
        $this->_assetRepo = $assetRepository;
        $this->_urlBuilder = $urlInterface;
        $this->_categoryResource = $categoryResource;
        $this->_objectManager = ObjectManager::getInstance();
        $this->_mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->_apiHelper = $apiHelper;
        $this->_ioFile = $ioFile;
        $this->_storeDetector = $storeDetector;
        $this->_storeManager->setCurrentStore(null);
        $this->jsonHelper = $jsonHelper;
        $this->blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function getHome()
    {
        $storeId = $this->_storeManager->getStore()->getId();
        $this->_appEmulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
        $home = $this->_homeFactory->create();
        /** Licenses */
        $home->setLicenses($this->_scopeConfig->getValue(self::MOBILE_APP_LICENSES_PATH));
        /** Token */
        $home->setToken($this->_scopeConfig->getValue(self::MOBILE_APP_TOKEN_PATH));
        /** Categories */
        $categoryCollection = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToFilter('include_in_menu', 1);

        $tree = $this->_treeFactory->create(['categoryCollection' => $categoryCollection]);
        $tree = $this->_categoryManagementFactory->create(['categoryTree' => $tree])->getTree();

        foreach ($categoryCollection as $category) {
            $this->_categories[$category->getId()] = $category;
        }

        $categories = $this->getCategoryNode($tree, $storeId);
        $home->setCategories([$categories]);
        /** Detect store data */
        $this->_storeDetector->detect($home);
        /** Config */
        $config = [
            'subscription' => [
            ]
        ];
        $home->setConfig([$config]);

        /** Banner slider */
        $banner_image = $this->getHomeBannerSlider();

        $home->setBannerSlider($banner_image);

        return $home;
    }

    /**
     * @inheritdoc
     */
    public function getHomeWidgetBestseller($limit, $page = 1)
    {
        $renderer = $this->_storeDetector->getStoreRenderer('product_sliders');
        if (!$renderer || !method_exists($renderer, 'getHomeWidgetBestseller')) {
            return false;
        }

        return $renderer->{'getHomeWidgetBestseller'}($limit, $page);
    }

    /**
     * @inheritdoc
     */
    public function getHomeWidgetNew($limit, $page = 1)
    {
        $renderer = $this->_storeDetector->getStoreRenderer('product_sliders');
        if (!$renderer || !method_exists($renderer, 'getHomeWidgetNew')) {
            return false;
        }

        return $renderer->{'getHomeWidgetNew'}($limit, $page);
    }

    /**
     * @inheritdoc
     */
    public function locations($storeType, $city, $cityOther, $district)
    {
        /** @var \Magenest\MapList\Controller\Index\GetLocation $controller */
        $controller = $this->_objectManager->get('Magenest\MapList\Controller\Index\GetLocation');
        $controller->getRequest()->setParams(['store_type' => $storeType, 'city' => $city, 'city_other' => $cityOther, 'district' => $district]);
        $this->_appEmulation->startEnvironmentEmulation($this->_storeManager->getStore()->getId(), Area::AREA_FRONTEND, true);

        return $this->_dataObjectFactory->create()
            ->addData(['result' => $controller->process()])
            ->getData();
    }

    /**
     * @inheritdoc
     */
    public function contact(ContactInterface $contact)
    {
        if (trim($contact->getName()) === '') {
            throw new LocalizedException(__('Enter the Name and try again.'));
        }

        if (trim($contact->getComment()) === '') {
            throw new LocalizedException(__('Enter the comment and try again.'));
        }

        if (false === \strpos($contact->getEmail(), '@')) {
            throw new LocalizedException(__('The email address is invalid. Verify the email address and try again.'));
        }

        $contactData = [
            'name' => $contact->getName(),
            'type' => $contact->getType(),
            'email' => $contact->getEmail(),
            'address' => $contact->getEmail(),
            'telephone' => $contact->getTelephone(),
            'topic' => $contact->getTopic(),
            'comment' => $contact->getComment()
        ];

        try {
            $this->_contactMail->send(
                $contactData['email'],
                ['data' => new DataObject($contactData)]
            );
            $this->_dataPersistor->clear('contact_us');
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->_dataPersistor->set('contact_us', $this->getRequest()->getParams());
            throw new LocalizedException(__('An error occurred while processing your form. Please try again later.'));
        }

        return true;
    }

    /**
     * Get category node
     *
     * @param \Magento\Catalog\Api\Data\CategoryTreeInterface $tree
     * @param int $storeId
     * @return array
     */
    private function getCategoryNode($tree, $storeId)
    {
        $category = $tree->getData();
        $category['children_data'] = [];
        $image = $this->_categoryResource->getAttributeRawValue($tree->getId(), 'image', $storeId);

        if (!empty($image)) {
            $category['image'] = $image;
        }

        if (!sizeof($tree->getChildrenData()) && $tree->getProductCount()) {
            //$products = [];
            //$collection = $this->_categories[$tree->getId()]->getProductCollection()
            //    ->addAttributeToFilter('status', ['in' => $this->_productStatus->getVisibleStatusIds()])
            //    ->setVisibility($this->_productVisibility->getVisibleInSiteIds())
            //    ->addAttributeToSelect('*');
            //
            //foreach ($collection as $product) {
            //    if ($product->isSaleable()) {
            //        $products[] = [
            //            'name' => $product->getName(),
            //            'sku' => $product->getSku(),
            //            'id' => $product->getId(),
            //            'imgUrl' => $product->getImage(),
            //            'smallImage' => $product->getSmallImage(),
            //            'urlKey' => $product->getUrlKey(),
            //            'status' => $product->getStatus(),
            //            'visibility' => $product->getVisibility(),
            //            'type_id' => $product->getTypeId(),
            //            'price' => $product->getFinalPrice()
            //        ];
            //    }
            //}
            //
            //$category['products'] = $products;
        }

        foreach ($tree->getChildrenData() as $childNode) {
            $category['children_data'][] = $this->getCategoryNode($childNode, $storeId);
        }

        return $category;
    }

    /**
     * Get banner slider image url
     */
    private function getHomeBannerSlider() {
        $block_banner_id = HomeBannerSlider::HOME_BANNER_SLIDER_ID;

        $block_content_html = $this->blockCollectionFactory->create()
                                                         ->addFieldToFilter('identifier', $block_banner_id)
                                                         ->setCurPage(1)
                                                         ->setPageSize(1)
                                                         ->getFirstItem()
                                                         ->getContent();

        $regex = '/(\w+)*?wysiwyg(.[^}]+)/';
        $media_url = preg_match_all($regex, $block_content_html, $result);
        if ($media_url && isset($result[0])) {
            return $result[0];
        }
        return [];
    }
}
