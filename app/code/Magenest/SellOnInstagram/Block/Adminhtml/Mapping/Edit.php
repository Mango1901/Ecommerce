<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Mapping;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template\Context;
use Magenest\SellOnInstagram\Helper\Data as HelperData;
use Magenest\SellOnInstagram\Model\Mapping as MappingModel;
use Magenest\SellOnInstagram\Helper\MappingAttribute;

/**
 * Class Edit
 * @package Magenest\SellOnInstagram\Block\Adminhtml\Mapping
 */
class Edit extends Template
{
    protected $templateMapping = null;
    /**
     * @var HelperData
     */
    protected $helperData;
    /**
     * @var MappingAttribute
     */
    protected $mappingAttribute;
    /**
     * @var Json
     */
    protected $jsonFramework;
    /**
     * @var Registry
     */
    protected $coreRegistry;

    public function __construct(
        HelperData $helperData,
        MappingAttribute $mappingAttribute,
        Json $jsonFramework,
        Registry $registry,
        Context $context,
        array $data = []
    )
    {
        $this->helperData = $helperData;
        $this->mappingAttribute = $mappingAttribute;
        $this->jsonFramework = $jsonFramework;
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }


    public function getMappingTemplateData()
    {
        $mappingModel = $this->getMappingTemplateModel();
        $mappingId = $mappingModel->getId();
        $this->jsLayout['components']['template_attributes'] = [
            'component' => 'Magenest_SellOnInstagram/js/mapping/mapping',
            'displayArea' => 'template_attributes',
            'template' => 'Magenest_SellOnInstagram/view/mapping/mapping'
        ];
        $this->jsLayout['components']['template_attributes']['config']['attributes'] = [
            'fb_attribute' => $this->mappingAttribute->getFbShoppingAttribute(),
            'magento_attribute' => $this->helperData->getProductAttribute(),
            'mapped_fields' => $this->helperData->getFieldsMappedByTemplateId($mappingId),
            'save_mapping_url' => $this->_urlBuilder->getUrl("*/*/save"),
            'back_mapping_url' => $this->_urlBuilder->getUrl("*/*/"),
            'get_mapping_url' => $this->_urlBuilder->getUrl("*/*/getFields"),
            'template_listing_url' => $this->_urlBuilder->getUrl("*/*/index"),
            'template_name' => $mappingModel->getName() ? $mappingModel->getName() : ''
        ];

        return $this->jsonFramework->serialize($this->jsLayout);
    }

    /**
     * @return mixed|null
     */

    private function getMappingTemplateModel()
    {
        if ($this->templateMapping == null) {
            $this->templateMapping = $this->coreRegistry->registry(MappingModel::REGISTER);
        }

        return $this->templateMapping;
    }

    /**
     * @return null
     */
    public function getMappingTemplateId()
    {
        return $this->getMappingTemplateModel()->getId() ?? null;
    }
}
