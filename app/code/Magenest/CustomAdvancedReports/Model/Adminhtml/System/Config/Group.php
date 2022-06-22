<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magenest\CustomAdvancedReports\Model\Adminhtml\System\Config;

use InvalidArgumentException;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class CountryCreditCard
 */
class Group extends Value
{
    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    private $rs;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\App\ResourceConnection $rs
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @param \Magento\Framework\Serialize\Serializer\Json $serializer
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        Random $mathRandom,
        \Magento\Framework\App\ResourceConnection $rs,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [],
        Json $serializer = null
    ) {
        $this->mathRandom = $mathRandom;
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(Json::class);
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
        $this->rs         = $rs;
        $this->connection = $rs->getConnection();
    }

    /**
     * Prepare data before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (!is_array($value)) {
            try {
                $value = $this->serializer->unserialize($value);
            } catch (InvalidArgumentException $e) {
                $value = [];
            }
        }
        $result = [];
        foreach ($value as $data) {
            if (empty($data['name']) || empty($data['category'])) {
                continue;
            }
            $country = $data['name'];
            if (array_key_exists($country, $result)) {
                $result[$country] = $this->appendUniqueCountries($result[$country], $data['category']);
            } else {
                $result[$country] = $data['category'];
            }
        }
        $this->importData($this->getValue());
        $this->setValue($this->serializer->serialize($result));
        return $this;
    }

    /**
     * Append unique countries to list of exists and reindex keys
     *
     * @param array $countriesList
     * @param array $inputCountriesList
     * @return array
     */
    private function appendUniqueCountries(array $countriesList, array $inputCountriesList)
    {
        $result = array_merge($countriesList, $inputCountriesList);
        return array_values(array_unique($result));
    }

    private function importData($value)
    {
        $groupCatGroupTableName = $this->rs->getTableName('group_category_config');
        $importData             = [];
        foreach ($value as $data) {
            if (is_array($data['category'] ?? [])) {
                foreach ($data['category'] as $cat) {
                    $importData[]= [
                        'name' => $data['name'],
                        'category' => $cat
                    ];

                }
            }
        }
        if (!empty($importData)) {
            $this->connection->delete(
                $groupCatGroupTableName,
                ['1 = 1']
            );
            $this->connection->insertMultiple($groupCatGroupTableName, $importData);
        }
    }

    /**
     * Process data after load
     *
     * @return $this
     */
    public function afterLoad()
    {
        if ($this->getValue()) {
            $value = $this->serializer->unserialize($this->getValue());
            if (is_array($value)) {
                $this->setValue($this->encodeArrayFieldValue($value));
            }
        }
        return $this;
    }

    /**
     * Encode value to be used in \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
     *
     * @param array $value
     * @return array
     */
    protected function encodeArrayFieldValue(array $value)
    {
        $result = [];
        foreach ($value as $country => $creditCardType) {
            $id          = $this->mathRandom->getUniqueHash('_');
            $result[$id] = ['name' => $country, 'category' => $creditCardType];
        }
        return $result;
    }
}
