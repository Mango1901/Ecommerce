<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Mapping;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as SerializerJson;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Psr\Log\LoggerInterface;
use Magenest\SellOnInstagram\Model\MappingFactory;
use Magenest\SellOnInstagram\Model\Mapping;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping as MappingResource;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping\CollectionFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Class AbstractMapping
 * @package Magenest\SellOnInstagram\Controller\Adminhtml\Mapping
 */
abstract class AbstractMapping extends Action
{
    const ADMIN_RESOURCE = "Magenest_SellOnInstagram::instagram_mapping";
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var MappingFactory
     */
    protected $mappingFactory;
    /**
     * @var MappingResource
     */
    protected $mappingResource;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var CollectionFactory
     */
    protected $mappingCollectionFactory;
    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;
    /**
     * @var SerializerJson
     */
    protected $jsonFramework;

    /**
     * AbstractMapping constructor.
     * @param PageFactory $resultPageFactory
     * @param LoggerInterface $logger
     * @param MappingFactory $mappingFactory
     * @param MappingResource $mappingResource
     * @param Registry $coreRegistry
     * @param Filter $filter
     * @param CollectionFactory $mappingCollectionFactory
     * @param ForwardFactory $resultForwardFactory
     * @param SerializerJson $jsonFramework
     * @param Context $context
     */

    public function __construct(
        PageFactory $resultPageFactory,
        LoggerInterface $logger,
        MappingFactory $mappingFactory,
        MappingResource $mappingResource,
        Registry $coreRegistry,
        Filter $filter,
        CollectionFactory $mappingCollectionFactory,
        ForwardFactory $resultForwardFactory,
        SerializerJson $jsonFramework,
        Context $context
    )
    {
        $this->logger = $logger;
        $this->resultPageFactory = $resultPageFactory;
        $this->mappingFactory = $mappingFactory;
        $this->mappingResource = $mappingResource;
        $this->coreRegistry = $coreRegistry;
        $this->filter = $filter;
        $this->mappingCollectionFactory = $mappingCollectionFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->jsonFramework = $jsonFramework;
        parent::__construct($context);
    }

    /**
     * @param $resultPage
     * @return mixed
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Sell On Instagram'), __('Sell On Instagram'));
        return $resultPage;
    }


    protected function registerTemplate(Mapping $data)
    {
        $this->coreRegistry->register(Mapping::REGISTER, $data);
    }
}
