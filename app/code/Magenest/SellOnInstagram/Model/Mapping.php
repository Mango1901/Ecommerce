<?php

namespace Magenest\SellOnInstagram\Model;

use Exception;
use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Catalog\Model\CategoryFactory as CatalogCategoryFactory;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping as MappingResourceModel;

/**
 * Class Mapping
 * @package Magenest\SellOnInstagram\Model
 */
class Mapping extends AbstractModel
{
    const TABLE = 'magenest_instagram_mapping_template';
    const REGISTER = 'mapping_template_model';
    const PRODUCT_TEMPLATE = 0;

    protected $mapping = null;
    /**
     * @var MappingResourceModel
     */
    protected $mappingResource;
    /**
     * @var Json
     */
    protected $jsonFramework;
    /**
     * @var CatalogCategoryFactory
     */
    protected $categoryFactory;


    public function __construct(
        MappingResourceModel $mappingResource,
        Json $jsonFramework,
        CatalogCategoryFactory $categoryFactory,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->mappingResource = $mappingResource;
        $this->jsonFramework = $jsonFramework;
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init('Magenest\SellOnInstagram\Model\ResourceModel\Mapping');
    }

    private function saveMappingAttributes(Mapping $mappingModel)
    {
        try {
            $templateId = $mappingModel->getId();
            $templateContent = $mappingModel->getContentTemplate();
            $attributesMapped = $this->mappingResource->getAllMagentoMappedFields($templateId);
            $attributesNew = [];
            if ($templateContent != '') {
                $templateContent = $this->jsonFramework->unserialize($templateContent);
                foreach ($templateContent as $content) {
                    $content['template_id'] = $mappingModel->getId();
                    $this->mappingResource->saveTemplateContent($content);
                    $attributesNew[] = $content['magento_attribute'];
                }
            }
            $diffArray = array_diff($attributesMapped, $attributesNew);
            $this->mappingResource->deleteTemplateContent($templateId, $diffArray);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
