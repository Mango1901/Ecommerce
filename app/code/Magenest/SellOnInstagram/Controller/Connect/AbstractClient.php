<?php
namespace Magenest\SellOnInstagram\Controller\Connect;

use Magenest\SellOnInstagram\Helper\Helper;
use Magenest\SellOnInstagram\Helper\Data;
use Magenest\SellOnInstagram\Logger\Logger;
use Magento\Framework\App\Action\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Cache\Manager;
use Magento\Framework\App\Cache\Type\Config;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\HTTP\Client\Curl;

abstract class AbstractClient extends Action
{
    const CONFIGURATION_SECTION = 'adminhtml/system_config/edit/section/sell_on_instagram';
    /**
     * @var Helper
     */
    protected $helper;
    /**
     * @var Manager
     */
    protected $cacheManager;
    /**
     * @var WriterInterface
     */
    protected $writer;
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var Json
     */
    protected $jsonFramework;
    /**
     * @var ResourceConnection
     */
    protected $resource;
    /**
     * @var Curl
     */
    protected $curl;
    /**
     * @var Data
     */
    protected $helperData;
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        Helper $helper,
        Data $helperData,
        Manager $cacheManager,
        WriterInterface $writer,
        Logger $logger,
        Registry $registry,
        PageFactory $resultPageFactory,
        Json $jsonFramework,
        ResourceConnection $resourceConnection,
        Curl $curl,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        Context $context
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->helperData = $helperData;
        $this->cacheManager = $cacheManager;
        $this->writer = $writer;
        $this->logger = $logger;
        $this->coreRegistry = $registry;
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonFramework = $jsonFramework;
        $this->resource = $resourceConnection;
        $this->curl = $curl;
        $this->urlBuilder = $urlBuilder;
    }

    /** Clean config cache */
    public function cleanConfigCache()
    {
        $this->cacheManager->clean([Config::TYPE_IDENTIFIER]);
    }
}
