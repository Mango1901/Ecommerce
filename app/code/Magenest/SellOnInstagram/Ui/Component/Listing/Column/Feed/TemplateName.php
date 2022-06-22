<?php

namespace Magenest\SellOnInstagram\Ui\Component\Listing\Column\Feed;

use Magento\Ui\Component\Listing\Columns\Column;
use Magenest\SellOnInstagram\Model\MappingFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magenest\SellOnInstagram\Model\ResourceModel\Mapping;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class TemplateName extends Column
{

    /**
     * @var MappingFactory
     */
    protected $templateFactory;
    /**
     * @var Mapping
     */
    protected $templateResource;

    public function __construct(
        MappingFactory $templateFactory,
        Mapping $templateResource,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    )
    {
        $this->templateFactory = $templateFactory;
        $this->templateResource = $templateResource;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $templateId = $item['template_id'];
                $item['template_id'] = $this->templateResource->getNameById($templateId);
            }
        }

        return $dataSource;
    }
}
