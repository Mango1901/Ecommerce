<?php
/**
 * @copyright Copyright (c) magenest.com, Inc. (https://www.magenest.com)
 */

namespace Magenest\CustomAdvancedReports\Block\Adminhtml\Form\Field;


use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

class Category extends Select
{
    /**
     * @var CollectionFactory
     */
    private $categoryCollectionFactory;

    public function __construct(
        Context $context,
        CollectionFactory $categoryCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value . '[]');
    }

    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getOptionCategory());
        }
        $this->setExtraParams('multiple="multiple"');
        return parent::_toHtml();
    }

    private function getOptionCategory()
    {
        $collection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('level', 2)
            ->addFieldToSelect('name');

        $options = [['label' => '', 'value' => '']];

        foreach ($collection as $category) {
            $options[] = [
                'label' => $category->getName(),
                'value' => $category->getId()
            ];
        }
        return $options;
    }
}
