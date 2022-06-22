<?php

namespace Magenest\CustomSource\Helper;

use Dotdigitalgroup\Email\Logger\Logger;
use Magenest\CustomSource\Plugin\SetAreaCodeContext;
use Magenest\Directory\Block\Adminhtml\Area\Field\Area;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * General most used helper to work with config data, saving updating and generating.
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const PATH_AREA_ENABLE = 'directory/areas/enable';
    const PATH_AREA_DATA = 'directory/areas/area';
    const PATH_DEFAULT_AREA = 'directory/areas/default_area';
    const PATH_AREA_ENABLE_PRODUCT_DETAIL = 'directory/areas/display_product_detail';

    /**
     * @var SerializerInterface
     */
    public $serializer;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var SourceRepositoryInterface
     */
    private $sourceRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var HttpContext
     */
    protected $context;

    /**
     * Data constructor.
     * @param Context $context
     * @param SerializerInterface $serializer
     * @param Logger $logger
     * @param SourceRepositoryInterface $sourceRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ResourceConnection $resourceConnection
     * @param HttpContext $httpContext
     */
    public function __construct(
        Context $context,
        SerializerInterface $serializer,
        Logger $logger,
        SourceRepositoryInterface $sourceRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ResourceConnection $resourceConnection,
        HttpContext $httpContext
    ) {
        $this->serializer = $serializer;
        $this->logger = $logger;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sourceRepository = $sourceRepository;
        $this->resourceConnection = $resourceConnection;
        $this->context = $httpContext;
        parent::__construct($context);
    }

    /**
     * @param int|null $storeId
     *
     * @return array
     */
    public function getAreaData($storeId = null)
    {
        $dataString = $this->scopeConfig->getValue(
            self::PATH_AREA_DATA,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        try {
            return $this->serializer->unserialize($dataString);
        } catch (\Exception $exception) {
            return $dataString ?? [];
        }
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function getDefaultArea($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::PATH_DEFAULT_AREA,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnablePopupArea($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_AREA_ENABLE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * @param int|null $storeId
     *
     * @return bool
     */
    public function isEnableOnProductDetail($storeId = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::PATH_AREA_ENABLE_PRODUCT_DETAIL,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get config scope value.
     *
     * @param string $path
     * @param string $contextScope
     * @param null $contextScopeId
     *
     * @return int|float|string|boolean
     */
    public function getConfigValue(
        $path,
        $contextScope = ScopeInterface::SCOPE_STORE,
        $contextScopeId = null
    ) {
        return $this->scopeConfig->getValue(
            $path,
            $contextScope,
            $contextScopeId
        );
    }

    /**
     * @param string $areaCode
     * @param array $skus
     * @return array
     */
    public function getDataIsSalableOfProduct($areaCode, $skus)
    {
        try {
            $sourceCodes = [];
            $criteria = $this->searchCriteriaBuilder->addFilter(Area::COLUMN_AREA_CODE, $areaCode)->create();
            $sources = $this->sourceRepository->getList($criteria)->getItems();
            foreach ($sources as $source) {
                if ($source->isEnabled()) {
                    $sourceCodes[] = $source->getSourceCode();
                }
            }
            $connection = $this->resourceConnection->getConnection();

            $select = $connection->select()->from(
                ['isi' => $connection->getTableName('inventory_source_item')],
                [
                    'sku' => 'isi.sku',
                    'is_in_stock' => 'isi.status',
                    'qty' => 'SUM(isi.quantity)',
                ]
            )->where(
                'isi.sku IN (?)',
                $skus
            )->where(
                'isi.source_code IN (?)',
                $sourceCodes
            )->where(
                'isi.status = ?',
                1
            )->group(
                ['isi.sku']
            );

            return $connection->fetchAll($select);
        } catch (\Exception $exception) {
            $this->_logger->error($exception->getMessage());
        }
        return [];
    }

    public function getAreaCodeBySourceCode($sourceCode)
    {
        $areaCode = $this->sourceRepository->get($sourceCode);

        return $areaCode;
    }

    public function getAreaCodeBySku($sku)
    {
        $areaCodeObject = [];
        $areaCodeArrayValue = [];
        $sourceCodes = $this->getSourceCodeBySku($sku);
        foreach ($sourceCodes as $sourceCode) {
            $areaCodeObject[] = $this->sourceRepository->get($sourceCode);
        }

        foreach ($areaCodeObject as $one) {
            $areaCodeArrayValue[] = $one->getData()["area_code"];
        }

        return $areaCodeArrayValue;
    }

    public function getSourceCodeBySku($sku)
    {
        $sourceCodes = [];
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            ['isi' => $connection->getTableName('inventory_source_item')],
            [
                'source_code' => 'isi.source_code'
            ]
        )->where(
            'isi.sku IN (?)',
            $sku
        )->where(
            'isi.status = ?',
            1
        );

        $inventorySourceItemData = $connection->fetchAll($select);
        foreach ($inventorySourceItemData as $data) {
            $sourceCodes[] = $data["source_code"];
        }


        return $sourceCodes;
    }

    public function getCurrentArea()
    {
        return $this->context->getValue(SetAreaCodeContext::AREA_CODE_CONTEXT) ?: $this->getDefaultArea();
    }
}
