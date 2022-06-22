<?php
namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab;

use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;
use Magento\Ui\Component\Layout\Tabs\TabWrapper;
use Magenest\SellOnInstagram\Model\InstagramFeed;
use Magento\Ui\Component\Layout\Tabs\TabInterface;


class InstagramProductStatusTab extends TabWrapper implements TabInterface
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var bool
     */
    protected $isAjaxLoaded = true;

    /**
     * InstagramProductStatusTab constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return $this->getFeedModel()->getId();
    }

    private function getFeedModel()
    {
        return $this->coreRegistry->registry(InstagramFeed::REGISTER);
    }

    /**
     * Return Tab label
     *
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Product Generated');
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('instagramshop/*/log', ['_current' => true]);
    }
}
