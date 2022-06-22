<?php


namespace Magenest\Affiliate\Model\Config\Source\Cms;

use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Block
 * @package Magenest\Affiliate\Model\Config\Source\Cms
 */
class Block implements ArrayInterface
{
    /**
     * @var BlockFactory
     */
    protected $_cms;

    /**
     * @var
     */
    protected $_options;

    /**
     * Block constructor.
     *
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        BlockFactory $blockFactory
    ) {
        $this->_cms = $blockFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $cmsBlock = $this->_cms->create();
        $cmsBlockCollection = $cmsBlock->getCollection();
        if (!$this->_options) {
            foreach ($cmsBlockCollection as $item) {
                $this->_options[] = [
                    'label' => $item->getData('title'),
                    'value' => $item->getData('identifier')
                ];
            }
        }

        return $this->_options;
    }
}
