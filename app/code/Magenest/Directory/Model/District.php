<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\Directory\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class District
 * @package Magenest\Directory\Model
 *
 * @method string getDefaultName()
 * @method string getName()
 * @method string getCityId()
 */
class District extends AbstractModel implements IdentityInterface, ValidatorInterface
{
    /** Table */
    const TABLE = 'directory_district_entity';

    /** Cache */
    const CACHE_TAG = 'directory_district_entity';

    /**
     * @var Validator
     */
    protected $_validator;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Validator $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Validator $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        $this->_validator = $validator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Magenest\Directory\Model\ResourceModel\District::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave()
    {
        $this->_validator->validate($this);
        return parent::beforeSave();
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredUniqueFields()
    {
        return ['code'];
    }
}
