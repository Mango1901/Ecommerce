<?php
/**
 * @author Magenest Team
 * @copyright Copyright (c) 2018 Magenest (https://www.magenest.com)
 * @package Magenest_Core
 */
// @codingStandardsIgnoreFile
namespace Magenest\Core\Helper;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\HTTP\Adapter\CurlFactory;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Data
 * @package Magenest\Core\Helper
 */
class Data extends AbstractHelper
{
    /** @Const */
    const BASE_URL = 'https://store.magenest.com/';
    const BASE_CHECKUPDATE_PATH = 'magenestcore/checkupdate';
    const BASE_CHECKNOTIFICATION_PATH = 'magenestcore/notification/get/module/';
    const CACHE_MODULE_IDENTIFIER = 'magenest_modules';
    const CACHE_TIME_IDENTIFIER = 'magenest_time';
    const SEC_DIFF = 86400;
    const CACHE_MODULE_NOTIFICATIONS_LASTCHECK = 'module_notifications_lastcheck';
    const XML_PATH_MAGENEST_CORE_NOTIFICATIONS_FREQUENCY = 'magenest_core/notifications/frequency';
    const CACHE_TAG = 'MAGENEST_TAGS';

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @var ModuleListInterface
     */
    protected $moduleList;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var CurlFactory
     */
    protected $curlFactory;

    /**
     * @var null|\Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency = null;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * Severities
     */
    protected $_severities = [
        MessageInterface::SEVERITY_CRITICAL => 'critical',
        MessageInterface::SEVERITY_MAJOR => 'major',
        MessageInterface::SEVERITY_MINOR => 'minor',
        MessageInterface::SEVERITY_NOTICE => 'notice',
    ];

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var array
     */
    protected $attributeValues;

    /**
     * @var \Magento\Eav\Api\AttributeOptionManagementInterface
     */
    protected $attributeOptionManagement;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory
     */
    protected $optionLabelFactory;

    /**
     * @var \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory
     */
    protected $optionFactory;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\Source\TableFactory
     */
    protected $tableFactory;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param CacheInterface $cache
     * @param Curl $curl
     * @param ModuleListInterface $moduleList
     * @param StoreManagerInterface $storeManager
     * @param CurlFactory $curlFactory
     * @param ResourceConnection $resource
     */
    public function __construct(
        Context $context,
        CacheInterface $cache,
        Curl $curl,
        ModuleListInterface $moduleList,
        StoreManagerInterface $storeManager,
        CurlFactory $curlFactory,
        ResourceConnection $resource,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository,
        \Magento\Eav\Api\AttributeOptionManagementInterface $attributeOptionManagement,
        \Magento\Eav\Api\Data\AttributeOptionLabelInterfaceFactory $optionLabelFactory,
        \Magento\Eav\Api\Data\AttributeOptionInterfaceFactory $optionFactory,
        \Magento\Eav\Model\Entity\Attribute\Source\TableFactory $tableFactory
    ) {
        parent::__construct($context);
        $this->cache = $cache;
        $this->curlClient = $curl;
        $this->moduleList = $moduleList;
        $this->storeManager = $storeManager;
        $this->curlFactory = $curlFactory;
        $this->resource = $resource;
        $this->_objectManager = ObjectManager::getInstance();
        $this->attributeRepository = $attributeRepository;
        $this->attributeOptionManagement = $attributeOptionManagement;
        $this->optionLabelFactory = $optionLabelFactory;
        $this->optionFactory = $optionFactory;
        $this->tableFactory = $tableFactory;
    }

    public function validateRule($rules)
    {
        $ruleId = $rules->getSalesruleId();
        if ($ruleId) {
            $rule = $this->_objectManager->create(\Magento\SalesRule\Model\Rule::class)->load($ruleId);
            $time = $this->_objectManager->create(\Magento\Framework\Stdlib\DateTime\DateTime::class)->gmtDate('Y-m-d');
            $fromDate = $rule->getFromDate();
            $toDate = $rule->getToDate();
            $_currentTime = strtotime($time);
            return $_currentTime >= strtotime($fromDate) && (!$toDate || strtotime($toDate) >= $_currentTime);
        }
        return false;
    }

    /**
     * Get config value
     *
     * @param $field
     * @param null $scopeValue
     * @param string $scopeType
     * @return array|mixed
     */
    public function getConfigValue($field, $scopeValue = null, $scopeType = ScopeInterface::SCOPE_STORE)
    {
        return $this->scopeConfig->getValue($field, $scopeType, $scopeValue);
    }

    /**
     * Check update
     *
     * @return bool
     */
    public function checkUpdate()
    {
        $lastSt = $this->cache->load(self::CACHE_TIME_IDENTIFIER);
        if (!$lastSt) {
            $this->cache->save(time(), self::CACHE_TIME_IDENTIFIER, [self::CACHE_TAG]);
            return true;
        }

        if ((time() - intval($lastSt)) > self::SEC_DIFF) {
            $this->cache->save(time(), self::CACHE_TIME_IDENTIFIER, [self::CACHE_TAG]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Get modules
     *
     * @return mixed
     */
    public function getModules()
    {
        $data = $this->cache->load(self::CACHE_MODULE_IDENTIFIER);
        if (!$data) {
            $data = $this->refreshModuleData();
        }

        $result = json_decode($data, true);

        return $result;
    }

    /**
     * Get curl client
     *
     * @return \Magento\Framework\HTTP\Client\Curl
     */
    public function getCurlClient()
    {
        if (!$this->curlClient) {
            $this->curlClient = new Curl();
        }

        $this->curlClient->setTimeout(2);

        return $this->curlClient;
    }

    /**
     * Get update url
     *
     * @return string
     */
    public static function getUpdateUrl()
    {
        return self::BASE_URL . self::BASE_CHECKUPDATE_PATH;
    }

    /**
     * Get module notification update url
     *
     * @return string
     */
    public static function getUpdateNotificationUrl()
    {
        return self::BASE_URL . self::BASE_CHECKNOTIFICATION_PATH;
    }

    /**
     * Refresh module data
     *
     * @return false|string
     */
    private function refreshModuleData()
    {
        $moduleInfo = $this->getModulesInfo();
        if ($moduleInfo) {
            $this->cache->save(json_encode($moduleInfo), self::CACHE_MODULE_IDENTIFIER, [self::CACHE_TAG]);
        }

        return json_encode($moduleInfo);
    }

    /**
     * Get module info
     *
     * @return array
     */
    private function getModulesInfo()
    {
        $modulesOut = [];

        foreach ($this->moduleList->getAll() as $module) {
            $moduleName = @$module['name'];
            if (strstr($moduleName, 'Magenest_') === false
                || $moduleName === 'Magenest_Core'
            ) {
                continue;
            }

            $modulePart = explode("_", $moduleName);
            $mName = @$modulePart[1];
            $modulesOut[] = $mName;
        }

        if (!empty($modulesOut)) {
            sort($modulesOut);
        }

        return $modulesOut;
    }

    /**
     * Check notification update
     *
     * @return $this
     */
    public function checkNotificationUpdate()
    {
        $modules = $this->getModules();
        $param = implode('-', $modules);

        $curl = $this->curlFactory->create();
        $curl->setConfig(['timeout' => 2]);
        $curl->write(\Zend_Http_Client::GET, Data::getUpdateNotificationUrl() . $param);
        $data = $curl->read();

        if ($data !== false) {
            $data = preg_split('/^\r?$/m', $data, 2);
            $data = trim(@$data[1]);
            $data = json_decode($data, true);

            if (count($data)) {
                foreach ($data as $value) {
                    $this->addNotification(@$value['id'], @$value['severity'], @$value['created_at'], @$value['title'], @$value['description'], @$value['url']);
                }
            }
        }

        $curl->close();

        return $this;
    }

    /**
     * Get severity
     *
     * @param int $severity
     * @return
     */
    public function getSeverity($severity = 3)
    {
        return @$this->_severities[$severity];
    }

    /**
     * Save notifications (if not exists)
     *
     * @param $data
     */
    public function parse($data)
    {
        $connection = $this->resource->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $table = $this->resource->getTableName('adminnotification_inbox');

        foreach ($data as $item) {
            $select = $connection->select()->from($table)->where('magenest_id = ?', $item['magenest_id']);

            $row = $connection->fetchRow($select);

            if (!$row) {
                $connection->insert($table, $item);
            }
        }
    }

    /**
     * Add new message
     *
     * @param $severity
     * @param $date
     * @param $title
     * @param $description
     * @param string $url
     * @return $this
     */
    public function addNotification($id, $severity, $date, $title, $description, $url = '')
    {
        if (!$this->getSeverity($severity)) {
            return $this;
        }

        if (is_array($description)) {
            $description = '<ul><li>' . implode('</li><li>', $description) . '</li></ul>';
        }

        $this->parse([
            [
                'magenest_id' => $id,
                'severity' => $severity,
                'date_added' => $date,
                'title' => $title,
                'description' => $description,
                'url' => $url,
                'is_magenest' => 1
            ],
        ]);

        return $this;
    }

    /**
     * Format price
     *
     * @param $amount
     * @param bool $includeContainer
     * @param null $scope
     * @param null $currency
     * @param int $precision
     * @return float
     */
    public function formatPrice($amount, $includeContainer = true, $scope = null, $currency = null, $precision = PriceCurrencyInterface::DEFAULT_PRECISION)
    {
        return $this->getPriceCurrency()->format($amount, $includeContainer, $precision, $scope, $currency);
    }

    /**
     * Get price currency
     *
     * @return \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public function getPriceCurrency()
    {
        if (is_null($this->_priceCurrency)) {
            $this->_priceCurrency = $this->_objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface');
        }

        return $this->_priceCurrency;
    }

    /**
     * Find or create a matching attribute option
     *
     * @param string $attributeCode Attribute the option should exist in
     * @param string $label Label to find or add
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createOrGetId($attributeCode, $labels)
    {
        if (count($labels) < 1) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Labels for %1 must not be empty.', $attributeCode)
            );
        }

        // Does it already exist?
        $optionId = $this->getOptionId($attributeCode, $labels[0]);

        if (!$optionId) {
            // If no, add it.
            $optionLabels = [];
            foreach ($labels as $key => $label) {
                /** @var \Magento\Eav\Model\Entity\Attribute\OptionLabel $optionLabel */
                $optionLabel = $this->optionLabelFactory->create();
                $optionLabel->setStoreId($this->getStoreId($key));
                $optionLabel->setLabel($label);
                array_push($optionLabels, $optionLabel);
            }
            $option = $this->optionFactory->create();
            $option->setLabel($label[0]);
            $option->setStoreLabels($optionLabels);
            $option->setSortOrder(0);
            $option->setIsDefault(false);

            $this->attributeOptionManagement->add(
                \Magento\Catalog\Model\Product::ENTITY,
                $this->getAttribute($attributeCode)->getAttributeId(),
                $option
            );

            // Get the inserted ID. Should be returned from the installer, but it isn't.
            $optionId = $this->getOptionId($attributeCode, $label, true);
        }

        return $optionId;
    }

    private function getStoreData()
    {
        $storeManagerDataList = $this->storeManager->getStores();
        $options = [];

        foreach ($storeManagerDataList as $key => $value) {
            $options[] = ['label' => $value['name'] . ' - ' . $value['code'], 'value' => $key];
        }
        return $options;
    }

    private function getStoreId($sortOrder)
    {
        $stores = $this->getStoreData();
        switch ($sortOrder) {
            case 0:
                return 0; // Admin
            case 2:
                return $stores[0]['value']; // Default Store View - default
            case 1:
                return $stores[1]['value']; // English - en
            default:
                return $stores[0]['value'];
        }
    }

    /**
     * Get attribute by code.
     *
     * @param string $attributeCode
     * @return \Magento\Catalog\Api\Data\ProductAttributeInterface
     */
    public function getAttribute($attributeCode)
    {
        return $this->attributeRepository->get($attributeCode)->setStoreId(0);
    }

    /**
     * Find the ID of an option matching $label, if any.
     *
     * @param string $attributeCode Attribute code
     * @param string $label Label to find
     * @param bool $force If true, will fetch the options even if they're already cached.
     * @return int|false
     */
    public function getOptionId($attributeCode, $label, $force = false)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute */
        $attribute = $this->getAttribute($attributeCode);

        // Build option array if necessary
        if ($force === true || !isset($this->attributeValues[ $attribute->getAttributeId() ])) {
            $this->attributeValues[ $attribute->getAttributeId() ] = [];

            // We have to generate a new sourceModel instance each time through to prevent it from
            // referencing its _options cache. No other way to get it to pick up newly-added values.

            /** @var \Magento\Eav\Model\Entity\Attribute\Source\Table $sourceModel */
            $sourceModel = $this->tableFactory->create();
            $sourceModel->setAttribute($attribute);

            foreach ($sourceModel->getAllOptions() as $option) {
                $this->attributeValues[ $attribute->getAttributeId() ][ $option['label'] ] = $option['value'];
            }
        }

        // Return option ID if exists
        if (isset($this->attributeValues[ $attribute->getAttributeId() ][ $label ])) {
            return $this->attributeValues[ $attribute->getAttributeId() ][ $label ];
        }

        // Return false if does not exist
        return false;
    }
}
