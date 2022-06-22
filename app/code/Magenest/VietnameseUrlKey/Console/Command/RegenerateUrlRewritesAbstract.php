<?php

namespace Magenest\VietnameseUrlKey\Console\Command;

use Magenest\VietnameseUrlKey\Helper\Regenerate as RegenerateHelper;
use Magenest\VietnameseUrlKey\Model\RegenerateCategoryRewrites;
use Magenest\VietnameseUrlKey\Model\RegenerateProductRewrites;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\State as AppState;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;

abstract class RegenerateUrlRewritesAbstract extends Command
{
    const INPUT_KEY_STOREID = 'store-id';
    const INPUT_KEY_REGENERATE_ENTITY_TYPE = 'entity-type';
    const INPUT_KEY_SAVE_REWRITES_HISTORY = 'save-old-urls';
    const INPUT_KEY_REINDEX = 'reindex';
    const INPUT_KEY_NO_PROGRESS = 'no-progress';
    const INPUT_KEY_CACHE_FLUSH = 'cache-flush';
    const INPUT_KEY_CACHE_CLEAN = 'cache-clean';
    const INPUT_KEY_CATEGORIES_RANGE = 'categories-range';
    const INPUT_KEY_PRODUCTS_RANGE = 'products-range';
    const INPUT_KEY_CATEGORY_ID = 'category-id';
    const INPUT_KEY_PRODUCT_ID = 'product-id';
    const INPUT_KEY_PRODUCT_SKU = 'product-sku';
    const INPUT_KEY_PRODUCT_BATCH = 'product-batch';
    const INPUT_KEY_REGENERATE_ENTITY_TYPE_PRODUCT = 'product';
    const INPUT_KEY_REGENERATE_ENTITY_TYPE_CATEGORY = 'category';

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\State $appState
     */
    protected $_appState;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var RegenerateHelper
     */
    protected $helper;

    /**
     * @var RegenerateProductRewrites
     */
    protected $regenerateProductRewrites;

    /**
     * @var RegenerateCategoryRewrites
     */
    protected $regenerateCategoryRewrites;

    /**
     * @var array
     */
    protected $_commandOptions = [];

    /**
     * @var array
     */
    protected $_errors = [];

    /**
     * @var array
     */
    protected $_consoleMsg = [];

    /**
     * @var \Magento\Framework\App\Cache\Manager
     */
    protected $cacheManager;

    /**
     * RegenerateUrlRewritesAbstract constructor.
     * @param ResourceConnection $resource
     * @param AppState $appState
     * @param StoreManagerInterface $storeManager
     * @param RegenerateHelper $helper
     * @param RegenerateCategoryRewrites $regenerateCategoryRewrites
     * @param RegenerateProductRewrites $regenerateProductRewrites
     * @param \Magento\Framework\App\Cache\Manager $cacheManager
     */
    public function __construct(
        ResourceConnection $resource,
        AppState $appState,
        StoreManagerInterface $storeManager,
        RegenerateHelper $helper,
        RegenerateCategoryRewrites $regenerateCategoryRewrites,
        RegenerateProductRewrites $regenerateProductRewrites,
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        parent::__construct();

        $this->_resource                  = $resource;
        $this->_appState                  = $appState;
        $this->_storeManager              = $storeManager;
        $this->helper                     = $helper;
        $this->regenerateCategoryRewrites = $regenerateCategoryRewrites;
        $this->regenerateProductRewrites  = $regenerateProductRewrites;
        $this->cacheManager               = $cacheManager;

        // set default config values
        $this->_commandOptions['entityType']       = 'product';
        $this->_commandOptions['saveOldUrls']      = false;
        $this->_commandOptions['runReindex']       = false;
        $this->_commandOptions['storesList']       = [];
        $this->_commandOptions['showProgress']     = true;
        $this->_commandOptions['runCacheClean']    = false;
        $this->_commandOptions['runCacheFlush']    = false;
        $this->_commandOptions['categoriesFilter'] = [];
        $this->_commandOptions['productsFilter']   = [];
        $this->_commandOptions['categoryId']       = null;
        $this->_commandOptions['productId']        = null;
        $this->_commandOptions['productSku']       = null;
    }

    /**
     * Get list of all stores id/code
     *
     * @return array
     */
    protected function _getAllStoreIds()
    {
        $result = [];

        $sql = $this->_resource->getConnection()->select()
            ->from($this->_resource->getTableName('store'), ['store_id', 'code'])
            ->order('store_id ASC');

        $queryResult = $this->_resource->getConnection()->fetchAll($sql);

        foreach ($queryResult as $row) {
            $result[(int)$row['store_id']] = $row['code'];
        }

        return $result;
    }

    /**
     * Generate range of ID's
     * @param string $idsRange
     * @param string $type
     * @return array
     */
    protected function _generateIdsRangeArray($idsRange, $type = 'product')
    {
        $result = $tmpIds = [];

        list($start, $end) = array_map('intval', explode('-', $idsRange, 2));

        if ($end < $start) {
            $end = $start;
        }

        for ($id = $start; $id <= $end; $id++) {
            $tmpIds[] = $id;
        }

        $tableName = $this->_resource->getTableName('catalog_' . $type . '_entity');
        $sql       = $this->_resource->getConnection()->select()->from($tableName)
            ->where('entity_id IN (?)', array_values($tmpIds))
            ->order('entity_id');

        $queryResult = $this->_resource->getConnection()->fetchAll($sql);

        foreach ($queryResult as $row) {
            $result[] = (int)$row['entity_id'];
        }

        if (count($result) == 0) {
            $this->_errors[] = __("ERROR: %type ID's in this range not exists", ['type' => ucfirst($type)]);
        }

        return $result;
    }

    /**
     * Collect console messages
     * @param mixed $msg
     * @return void
     */
    protected function _addConsoleMsg($msg)
    {
        if ($msg instanceof \Magento\Framework\Phrase) {
            $msg = $msg->render();
        }

        $this->_consoleMsg[] = (string)$msg;
    }

    /**
     * Display all console messages
     * @return void
     */
    protected function _displayConsoleMsg()
    {
        if (count($this->_consoleMsg) > 0) {
            $this->_output->writeln('[CONSOLE MESSAGES]');
            foreach ($this->_consoleMsg as $msg) {
                $this->_output->writeln($msg);
            }
            $this->_output->writeln('[END OF CONSOLE MESSAGES]');
            $this->_output->writeln('');
            $this->_output->writeln('');
        }
    }

    /**
     * Run reindexation
     * @return void
     */
    protected function _runReindexation()
    {
        if ($this->_commandOptions['runReindex'] == true) {
            $this->_output->write('Reindexation...');
            $this->_output->write('Indexation isn\'t necessary, break.');
            $this->_output->writeln(' Done');
        }
    }

    /**
     * Clear cache
     * @return void
     */
    protected function _runClearCache()
    {
        if ($this->_commandOptions['runCacheClean'] || $this->_commandOptions['runCacheFlush']) {
            $this->_output->write('Cache refreshing...');
            if ($this->_commandOptions['runCacheClean']) {
                $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
            }
            if ($this->_commandOptions['runCacheFlush']) {
                $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
            }
            $this->_output->writeln(' Done');
        }
    }
}
