<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Feed;


use Magenest\SellOnInstagram\Model\InstagramFeedFactory;
use Magenest\SellOnInstagram\Model\InstagramFeed;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed as InstagramResourceModel;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory as ViewLayoutFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magento\Framework\View\Result\PageFactory;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\View\Result\LayoutFactory;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed\CollectionFactory as InstagramFeedCollectionFactory;
use Magenest\SellOnInstagram\Helper\Data;
use Magenest\SellOnInstagram\Model\ProductBatch;
use Magenest\SellOnInstagram\Model\History;
use Magenest\SellOnInstagram\Model\BatchBuilder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
abstract class AbstractFeed extends Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magenest_SellOnInstagram::instagram_feed';
    /**
     * @var InstagramFeedFactory
     */
    protected $instagramFeedFactory;
    /**
     * @var InstagramResourceModel
     */
    protected $instagramFeedResource;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var RuleFactory
     */
    protected $ruleFactory;
    /**
     * @var LayoutFactory
     */
    protected $resultLayoutFactory;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var InstagramFeedCollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;
    /**
     * @var SerializerJson
     */
    protected $jsonFramework;
    /**
     * @var RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var ViewLayoutFactory
     */
    protected $layoutFactory;
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var History
     */
    protected $historyModel;
    /**
     * @var ProductBatch
     */
    protected $productBatch;
    /**
     * @var BatchBuilder
     */
    protected $batchBuilder;
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;


    public function __construct(
        InstagramFeedFactory $instagramFeedFactory,
        InstagramResourceModel $instagramFeedResource,
        Registry $registry,
        PageFactory $resultPageFactory,
        LoggerInterface $logger,
        RuleFactory $ruleFactory,
        LayoutFactory $resultLayoutFactory,
        Filter $filter,
        InstagramFeedCollectionFactory $collectionFactory,
        ForwardFactory $resultForwardFactory,
        SerializerJson $jsonFramework,
        RawFactory $resultRawFactory,
        ViewLayoutFactory $layoutFactory,
        Data $helper,
        History $historyModel,
        ProductBatch $productBatch,
        BatchBuilder $batchBuilder,
        ProductCollectionFactory $productCollectionFactory,
        StockRegistryInterface $stockRegistry,
        Context $context
    )
    {
        $this->instagramFeedFactory = $instagramFeedFactory;
        $this->instagramFeedResource = $instagramFeedResource;
        $this->coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->logger = $logger;
        $this->ruleFactory = $ruleFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->jsonFramework = $jsonFramework;
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
        $this->helper = $helper;
        $this->historyModel = $historyModel;
        $this->productBatch = $productBatch;
        $this->batchBuilder = $batchBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->stockRegistry = $stockRegistry;
        parent::__construct($context);
    }

    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Manage Feed'), __('Manage Feed'))
            ->addBreadcrumb(__('Feed'), __('Feed'));
        return $resultPage;
    }

    protected function initCurrentFeed()
    {
        $id = $this->getRequest()->getParam('id');
        $instagramFeedModel = $this->instagramFeedFactory->create();
        if ($id) {
            $this->instagramFeedResource->load($instagramFeedModel, $id);
        }
        $this->coreRegistry->register(InstagramFeed::REGISTER, $instagramFeedModel);
        return $instagramFeedModel;
    }

    public function getFeedModel()
    {
        $id = $this->getRequest()->getParam('id');
        $instagramFeedModel = $this->instagramFeedFactory->create();
        if ($id) {
            $this->instagramFeedResource->load($instagramFeedModel, $id);
            if (!$instagramFeedModel->getId()) {
                throw new \Exception(__("Can not find Feed"));
            }
        }
        return $instagramFeedModel;
    }
}
