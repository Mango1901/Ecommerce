<?php

namespace Magenest\CustomWordPress\Helper;

use FishPig\WordPress\Model\ResourceModel\Term\CollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;

/**
 * Class Data
 * @package Magenest\CustomWordPress\Helper
 */
class Data extends AbstractHelper
{
    protected $termFactory;
    protected $registry;

    /**
     * Data constructor.
     * @param Context $context
     * @param CollectionFactory $collection
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        CollectionFactory $collection,
        Registry $registry
    ) {
        $this->registry = $registry;
        $this->termFactory = $collection;
        parent::__construct($context);
    }

    public function getCategories()
    {
        $collection = $this->termFactory->create()
            ->addTaxonomyFilter('category');

        $collection->getSelect()
            ->distinct()
            ->join(
                array('relationship' => $collection->getTable('wordpress_term_relationship')),
                '`taxonomy`.`term_taxonomy_id` = `relationship`.`term_taxonomy_id`',
                ''
            )
            ->reset('order')
            ->order('name ASC');
        if ($collection->getFirstItem()->getId()) {
            $this->registry->register('first_cat_id', $collection->getFirstItem()->getId());
        }
        return $collection;
    }

    public function getFirstCategoryId()
    {
        return $this->registry->registry('first_cat_id');
    }
}
