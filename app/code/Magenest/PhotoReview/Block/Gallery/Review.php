<?php
namespace Magenest\PhotoReview\Block\Gallery;

use Magento\Review\Block\Product\View\ListView;

class Review extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory  */
    protected $_reviewCollection;

    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $resource;

    protected $_collection = null;

    protected $_pager;

    public function __construct(
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ){
        $this->_reviewCollection = $reviewCollection;
        $this->resource = $resource;
        parent::__construct($context, $data);
    }
    public function _construct()
    {
        parent::_construct();
        $this->setCollection($this->getViewParam());
        $this->setPhotoPerPages(20);
    }

    /**
     * Add rate votes
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->getCollection()->load()->addRateVotes();
        return parent::_beforeToHtml();
    }
    /**
     * @param int $pager
     */
    public function setPhotoPerPages($pager)
    {
        $this->_pager = $pager;
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var \Magento\Theme\Block\Html\Pager */
        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'photoreview.list.pager'
        );
        $pager->setUseContainer(false)
            ->setShowPerPage(false)
            ->setShowAmounts(false)
            ->setFrameLength(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setJump(
                $this->_scopeConfig->getValue(
                    'design/pagination/pagination_frame_skip',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                )
            )->setLimit(
                $this->_pager
            )->setCollection(
                $this->getCollection()
            );
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function setCollection()
    {
        if($this->_collection == null){
            $this->_collection = $this->_reviewCollection->create()
                ->addStoreFilter(
                    $this->_storeManager->getStore()->getId()
                )->addStatusFilter(
                \Magento\Review\Model\Review::STATUS_APPROVED
                )->setOrder('review_id', 'DESC');
            $this->addFilterPhoto();
        }
        return $this->_collection;
    }
    public function addFilterPhoto()
    {
        $photoReview = $this->resource->getTableName('magenest_photoreview_photo');
        $this->_collection->getSelect()->join(
            ['photo' => $photoReview],
            'main_table.review_id = photo.review_id',
            []
        )->distinct(true);
    }
    public function getCollection()
    {
        return $this->_collection;
    }
    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    /**
     * @return string
     */
    public function getViewParam()
    {
        return $this->getRequest()->getParam('view');
    }

    /**
     * @return int
     */
    public function getPageParam()
    {
        return $this->getRequest()->getParam('page');
    }

}