<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Magento\Backend\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\Renderer\ProductId;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramProduct\CollectionFactory as InstagramProductCollectionFactory;

class FeedLog extends Extended
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var InstagramProductCollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Registry
     */
    protected $coreRegistry;

    public function __construct(
        LoggerInterface $logger,
        InstagramProductCollectionFactory $instagramProductCollectionFactory,
        Registry $coreRegistry,
        Context $context,
        Data $backendHelper,
        array $data = []
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $instagramProductCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('instagramshop/*/template', ['_current' => true]);
    }

    /**
     * @return string
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        if ($this->getFilterVisibility()) {
            $html .= $this->getSearchButtonHtml();
            $html .= $this->getResetFilterButtonHtml();
        }

        return $html;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('instagram_feed_edit_tab_log');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setUseAjax(true);
    }

    /**
     * @return FeedLog
     */
    protected function _prepareCollection()
    {
        try {
            $param = $this->getRequest()->getParams();
            $collection = $this->collectionFactory->create()->addFieldToFilter('feed_id', $param['id']);
        } catch (Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    /**
     * @return FeedLog
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID #'),
                'width' => '150px',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'sku',
                'filter' => false,
                'renderer' => ProductId::class,
            ]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'index' => 'sku'
            ]
        );
        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }
        parent::_prepareColumns();

        return $this;
    }
}
