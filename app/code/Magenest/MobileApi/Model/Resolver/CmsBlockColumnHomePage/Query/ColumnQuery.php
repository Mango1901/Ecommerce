<?php

namespace Magenest\MobileApi\Model\Resolver\CmsBlockColumnHomePage\Query;

use Magenest\MobileApi\Setup\Patch\Data\HuntSaleImmediatelyHomePage;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchResultFactory;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;

class ColumnQuery implements ColumnQueryInterface
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


    public function __construct(
        CmsCollection $blockCollectionFactory,
        StoreManagerInterface $storeManager,
        SearchResultFactory $searchResultFactory,
        ReadExtensions $readExtensions
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->storeManager = $storeManager;
        $this->searchResultFactory = $searchResultFactory;
        $this->_readExtensions = $readExtensions;
    }

    public function getResult($args, $info, $context)
    {
        $identifier = HuntSaleImmediatelyHomePage::HUNT_SALE_IMMEDIATELY_HOME_PAGE_MOBILE;

        if (isset($args['identifier']) && $args['identifier'] != '') {
            $identifier = $args['identifier'];
        }

        $block = $this->blockCollectionFactory->create()
            ->addFieldToFilter(
                'identifier',
                $identifier
            )
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();

        $blockContentHtml = str_replace(["\r", "\n"], '', $block->getContent());

        $pageBuilderColumnsRegex = '/<div class=\"pagebuilder-column\"((.|\n)*?)<\/div>/';
        $desktopImagesRegex = '/<img class=\"pagebuilder-mobile-hidden\"([^\>]*)>/';
        $mobileImagesRegex = '/<img class=\"pagebuilder-mobile-only\"([^\>]*)>/';
        $contentsRegex = '/<div data-content-type=\"text\"(.*?)<\/div>/';
        $linkRegex = '/{{widget(.[^>]+)/';

        $result = [];

        $isExistsColumn = preg_match_all($pageBuilderColumnsRegex, $blockContentHtml, $pageBuilderColumns);
        if ($isExistsColumn) {
            foreach ($pageBuilderColumns[0] as $index => $item) {
                preg_match('/data-background-images=\"([^"]*)"/', $item, $match);
                $result[$index]['background_image'] = end($match) ?? '';

                preg_match($desktopImagesRegex, $item, $match);
                preg_match('/src=\"([^"]*)"/', end($match), $match);
                $result[$index]['desktop_image'] = end($match) ?? '';

                preg_match($mobileImagesRegex, $item, $match);
                preg_match('/src=\"([^"]*)"/', end($match), $match);
                $result[$index]['mobile_image'] = end($match) ?? '';

                $inExistContent = preg_match($contentsRegex, $item, $match);

                if ($inExistContent) {
                    $content = trim(strip_tags($match[0]));
                    if (preg_match('/<strong(>|\s+[^>]*>).*?<\/strong>/', $match[0], $contentStrong)) {
                        $result[$index]['content'] = trim(strip_tags($contentStrong[0]));
                        $result[$index]['more_info'] = trim(str_replace($result[$index]['content'], '', $content));
                    } else {
                        $result[$index]['content'] = $content;
                    }
                }

                $isExistsLink = preg_match($linkRegex, $item, $pageBuilderLink);
                if ($isExistsLink) {
                    $isExistValue = preg_match('/id_path=\'([^\']*)\'/', end($pageBuilderLink), $pageBuilderLinkValue);
                    if ($isExistValue) {
                        $linkArray = explode('/', end($pageBuilderLinkValue));
                        $result[$index]['link']['value'] = $linkArray[1];
                        $result[$index]['link']['type'] = $linkArray[0];
                    }
                } else {
                    $isExistUrl = preg_match('/href=\"([^"]*)"/', $item, $urlLink);
                    if ($isExistUrl) {
                        $result[$index]['link']['value'] = end($urlLink) ?? null;
                        $result[$index]['link']['type'] = 'url';
                    }
                }
            }
        }

        return [
            'columnsSearchResult' => $result,
            'block_id' => $block->getId(),
            'identifier' => $block->getIdentifier()
        ];
    }
}
