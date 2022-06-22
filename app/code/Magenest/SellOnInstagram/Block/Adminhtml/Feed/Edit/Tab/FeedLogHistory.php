<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Framework\Registry;
use Magento\Backend\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magenest\SellOnInstagram\Model\Config\Source\Status as InstagramStatus;
use Magenest\SellOnInstagram\Model\ResourceModel\History\CollectionFactory;
use Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\Renderer\SyncMode;
use Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Tab\Renderer\ActionType;

class FeedLogHistory extends Extended
{
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var InstagramStatus
     */
    protected $instagramStatus;

    public function __construct(
        InstagramStatus $instagramStatus,
        LoggerInterface $logger,
        CollectionFactory $instagramHistoryCollectionFactory,
        Registry $coreRegistry,
        Context $context,
        Data $backendHelper,
        array $data = []
    )
    {
        $this->logger = $logger;
        $this->collectionFactory = $instagramHistoryCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->instagramStatus = $instagramStatus;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @inheritdoc
     */
    public function getGridUrl()
    {
        return $this->getUrl('instagramshop/*/block', ['_current' => true]);
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
        $this->setId('instagram_feed_history_tab');
        $this->setDefaultSort('history_id');
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
            'history_id', [
                'header' => __('ID #'),
                'width' => '150px',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
                'index' => 'history_id',
                'filter' => false,
            ]
        );

        $this->addColumn(
            'created_at', [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type'   => 'datetime',
            ]
        );
        $this->addColumn(
            'user_id', [
                'header' => __('Sync Mode'),
                'index' => 'user_id',
                'renderer' => SyncMode::class,
            ]
        );
        $this->addColumn(
            'action', [
                'header' => __('Action Type'),
                'index' => 'action',
                'renderer' => ActionType::class
            ]
        );
        $this->addColumn(
            'execution_time', [
                'header' => __('Execution Time'),
                'index' => 'execution_time'
            ]
        );
        $this->addColumn(
            'error_products', [
                'header' => __('Error Log'),
                'index' => 'error_products'
            ]
        );
        $this->addColumn(
            'summary', [
                'header' => __('Process'),
                'index' => 'summary'
            ]
        );


        $block = $this->getLayout()->getBlock('grid.bottom.links.log');
        if ($block) {
            $this->setChild('grid.bottom.links.log', $block);
        }
        parent::_prepareColumns();

        return $this;
    }
}
