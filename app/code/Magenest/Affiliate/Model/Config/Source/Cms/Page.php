<?php


namespace Magenest\Affiliate\Model\Config\Source\Cms;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Page
 * @package Magenest\Affiliate\Model\Config\Source\Cms
 */
class Page implements ArrayInterface
{
    /**
     * @var PageFactory
     */
    protected $_cms;

    /**
     * @var
     */
    protected $_options;

    /**
     * Page constructor.
     *
     * @param PageFactory $pageFactory
     */
    public function __construct(PageFactory $pageFactory)
    {
        $this->_cms = $pageFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $this->_options = [];
        $existingIdentifiers = [];
        $cmsPage = $this->_cms->create();
        $cmsPageCollection = $cmsPage->getCollection();
        foreach ($cmsPageCollection as $item) {
            $identifier = $item->getData('identifier');

            $data['value'] = $identifier;
            $data['label'] = $item->getData('title');

            if (in_array($identifier, $existingIdentifiers)) {
                $data['value'] .= '|' . $item->getData('page_id');
            } else {
                $existingIdentifiers[] = $identifier;
            }

            $this->_options[] = $data;
        }

        return $this->_options;
    }
}
