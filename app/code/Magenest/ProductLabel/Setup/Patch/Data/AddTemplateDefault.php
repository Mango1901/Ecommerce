<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ProductLabel extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_ProductLabel
 */

namespace Magenest\ProductLabel\Setup\Patch\Data;

use Magenest\ProductLabel\Api\Data\ConstantInterface;
use Magenest\ProductLabel\Api\Data\LabelInterface;
use Magenest\ProductLabel\Model\ResourceModel\Label as ResourceLabel;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class AddTemplateDefault
 * @package Magenest\ProductLabel\Setup\Patch\Data
 */
class AddTemplateDefault implements DataPatchInterface
{

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var Config
     */
    private $eavConfig;

    /**
     * @var ResourceLabel
     */
    protected $label;

    /**
     * @var \Magenest\ProductLabel\Model\LabelFactory
     */
    protected $labelFactory;

    /**
     * @var \Magenest\ProductLabel\Model\LabelCategoryFactory
     */
    protected $labelCategoryFactory;

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelCategory
     */
    protected $labelCategoryResource;

    /**
     * @var \Magenest\ProductLabel\Model\LabelProductFactory
     */
    protected $labelProductFactory;

    /**
     * @var \Magenest\ProductLabel\Model\ResourceModel\LabelProduct
     */
    protected $labelProductResource;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * AddTemplateDefault constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param \Magenest\ProductLabel\Model\LabelFactory $labelFactory
     * @param \Magenest\ProductLabel\Model\LabelCategoryFactory $labelCategoryFactory
     * @param \Magenest\ProductLabel\Model\ResourceModel\LabelCategory $labelCategoryResource
     * @param \Magenest\ProductLabel\Model\LabelProductFactory $labelProductFactory
     * @param \Magenest\ProductLabel\Model\ResourceModel\LabelProduct $labelProductResource
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Framework\App\State $state
     * @param ResourceLabel $label
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Config $eavConfig,
        \Magenest\ProductLabel\Model\LabelFactory $labelFactory,
        \Magenest\ProductLabel\Model\LabelCategoryFactory $labelCategoryFactory,
        \Magenest\ProductLabel\Model\ResourceModel\LabelCategory $labelCategoryResource,
        \Magenest\ProductLabel\Model\LabelProductFactory $labelProductFactory,
        \Magenest\ProductLabel\Model\ResourceModel\LabelProduct $labelProductResource,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\State $state,
        ResourceLabel $label
    )
    {
        $this->setup = $setup;
        $this->eavConfig = $eavConfig;
        $this->labelFactory = $labelFactory;
        $this->labelCategoryFactory = $labelCategoryFactory;
        $this->labelCategoryResource = $labelCategoryResource;
        $this->labelProductFactory = $labelProductFactory;
        $this->labelProductResource = $labelProductResource;
        $this->label = $label;
        $this->_resource = $resource;
        $this->state = $state;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function apply()
    {
        $connection = $this->_resource->getConnection();
        try {
            $this->state->getAreaCode();
        } catch (\Exception $exception) {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }
        if ($connection->isTableExists($this->_resource->getTableName('magenest_product_label'))) {
            for ($templateDefault = 1; $templateDefault <= 3; $templateDefault++) {
                if ($templateDefault == 1) {
                    $name = LabelInterface::NEW_LABEL;
                    $labelType = ConstantInterface::PRODUCT_LABEL_NEW_TYPE;
                    $imageCategory = LabelInterface::IMAGE_LABEL_NEW;
                    $imageProduct = LabelInterface::IMAGE_NEW_PRODUCT;
                } elseif ($templateDefault == 2) {
                    $name = LabelInterface::SALE_LABEL;
                    $labelType = ConstantInterface::PRODUCT_LABEL_SALE_TYPE;
                    $imageCategory = LabelInterface::IMAGE_LABEL_SALE;
                    $imageProduct = LabelInterface::IMAGE_SALE_PRODUCT;
                } else {
                    $name = LabelInterface::BEST_SELLER_LABEL;
                    $labelType = ConstantInterface::PRODUCT_LABEL_BEST_SELLER;
                    $imageCategory = LabelInterface::IMAGE_LABEL_BEST_SELLER;
                    $imageProduct = LabelInterface::IMAGE_BEST_SELLER_PRODUCT;
                }

                //Save data in table magenest_product_label
                $templateData = [
                    'name' => $name,
                    'status' => 0,
                    'conditions_serialized' => '',
                    'priority' => 0,
                    'label_type' => $labelType
                ];
                $model = $this->labelFactory->create();
                $model->addData($templateData);
                $this->label->save($model);
                $labelId = $model->getData('label_id');
                $this->updateTableCategory($labelId, $imageCategory);
                $this->updateTableProduct($labelId, $imageProduct);
            }
        }
    }

    /**
     * @param $labelId
     * @param $imageCategory
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateTableCategory($labelId, $imageCategory) {
        $dataLabelCategory = $this->labelCategoryFactory->create();
        $this->labelCategoryResource->load($dataLabelCategory, $labelId, 'label_id');

        $arrCategory = [
            'position' => LabelInterface::POSITION_DEFAULT,
            'type' => LabelInterface::TYPE_DEFAULT,
            'image' => $imageCategory,
            'label_size' => LabelInterface::LABEL_SIZE_DEFAULT,
            'use_default' => LabelInterface::USE_DEFAULT
        ];
        $dataLabelCategory->addData($arrCategory);
        $this->labelCategoryResource->save($dataLabelCategory);
    }

    /**
     * @param $labelId
     * @param $imageProduct
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function updateTableProduct($labelId, $imageProduct) {
        $dataLabelProduct = $this->labelProductFactory->create();
        $this->labelProductResource->load($dataLabelProduct, $labelId, 'label_id');

        $arrProduct = [
            'position' => LabelInterface::POSITION_DEFAULT,
            'type' => LabelInterface::TYPE_DEFAULT,
            'image' => $imageProduct,
            'label_size' => LabelInterface::LABEL_SIZE_DEFAULT,
            'use_default' => LabelInterface::USE_DEFAULT
        ];

        $dataLabelProduct->addData($arrProduct);
        $this->labelProductResource->save($dataLabelProduct);
    }

    /**
     * @return array|string[]
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array|string[]
     */
    public function getAliases()
    {
        return [];
    }
}
