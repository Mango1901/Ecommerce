<?php
namespace Magenest\MobileApi\Model\Resolver\HotNewsHomePage\Query;

use Magenest\MobileApi\Setup\Patch\Data\HotNewsHomePage;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchResultFactory;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;
use FishPig\WordPress\Model\ResourceModel\Post\CollectionFactory;

class PostsQuery implements PostsQueryInterface
{
    /** @var CmsCollection */
    protected $blockCollectionFactory;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var SearchResultFactory  */
    private $searchResultFactory;

    /**
     * @var ReadExtensions
     */
    protected $_readExtensions;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        CmsCollection $blockCollectionFactory,
        StoreManagerInterface $storeManager,
        SearchResultFactory $searchResultFactory,
        ReadExtensions $readExtensions,
        CollectionFactory $collectionFactory
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->storeManager = $storeManager;
        $this->searchResultFactory = $searchResultFactory;
        $this->_readExtensions = $readExtensions;
        $this->collectionFactory = $collectionFactory;
    }

    public function getResult($args, $info, $context)
    {
        $identifier = HotNewsHomePage::HOT_NEWS_HOME_PAGE_MOBILE;

        $block = $this->blockCollectionFactory->create()
            ->addFieldToFilter(
                'identifier',
                $identifier
            )
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();

        $blockContentHtml = $block->getContent();
        $regex = '/(\w+)*?{{widget(.[^}}]+)/';
        $mainWidget = preg_match_all($regex, $blockContentHtml, $result);
        if ($mainWidget && isset($result[0][0])) {
            $postLimit = (int) filter_var($result[0][0], FILTER_SANITIZE_NUMBER_INT);
            if (!$postLimit) {
                $postLimit = 6;
            }
            $resultCollection = $this->collectionFactory->create()
                ->addPostTypeFilter('post')
                ->setOrderByPostDate()
                ->addIsViewableFilter()
                ->setCurPage(1)
                ->setPageSize($postLimit);

            $postsSearchResult = [];
            foreach ($resultCollection->getItems() as $item) {
                if ($image = $item->getImage()) {
                    $item['image'] = $image->getResizer() ?
                        $image->getResizer()->constrainOnly(true)->keepFrame(false)->keepAspectRatio(true)->resize(600, null) : '';
                }
                $postsSearchResult[] = $item->getData();
            }
        }
        return [
            'postsSearchResult' => $postsSearchResult,
            'block_id' => $block->getId(),
            'identifier' => $block->getIdentifier()
        ];
    }
}
