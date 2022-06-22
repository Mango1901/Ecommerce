<?php
namespace Magenest\MobileApi\Model\Resolver\Banner\Query;

use Magenest\MobileApi\Setup\Patch\Data\BannerHomePage;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogGraphQl\Model\Resolver\Products\SearchResultFactory;
use Magento\Framework\EntityManager\Operation\Read\ReadExtensions;

class BannerQuery implements BannerQueryInterface
{
    /** @var CmsCollection */
    protected $blockCollectionFactory;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    public function __construct(
        CmsCollection $blockCollectionFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->storeManager = $storeManager;
    }

    public function getResult($args, $info, $context)
    {
        $identifier = BannerHomePage::BANNER_HOME_PAGE_MOBILE;

        $block = $this->blockCollectionFactory->create()
            ->addFieldToFilter(
                'identifier',
                $identifier
            )
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();

        $blockContentHtml = $block->getContent();

        $desktopImagesRegex = '/<img class=\"pagebuilder-mobile-hidden\"([^\>]*)>/';
        $mobileImagesRegex = '/<img class=\"pagebuilder-mobile-only\"([^\>]*)>/';
        $captionImagesRegex = '/<figcaption data-element=\"caption\">(.*?)<\/figcaption>/';
        $linkRegex = '/{{widget(.[^>]+)/';

        $result = [
            'block_id' => $block->getId(),
            'identifier' => $block->getIdentifier()
        ];

        $isExistsDesktopImage = preg_match($desktopImagesRegex, $blockContentHtml, $pageBuilderDesktopImage);
        if ($isExistsDesktopImage) {
            preg_match('/src=\"([^"]*)\"/', end($pageBuilderDesktopImage), $desktopImage);
            $result['desktop_image'] = end($desktopImage) ?? '';
        }

        $isExistsMobileImage = preg_match($mobileImagesRegex, $blockContentHtml, $pageBuilderMobileImage);
        if ($isExistsMobileImage) {
            preg_match('/src=\"([^"]*)\"/', end($pageBuilderMobileImage), $mobileImage);
            $result['mobile_image'] = end($mobileImage) ?? '';
        }

        $isExistsCaptionImage = preg_match($captionImagesRegex, $blockContentHtml, $pageBuilderCaptionImage);
        if ($isExistsCaptionImage) {
            $result['image_caption'] = trim(strip_tags(end($pageBuilderCaptionImage)));
        }

        $isExistsLink = preg_match($linkRegex, $blockContentHtml, $pageBuilderLink);
        if ($isExistsLink) {
            $isExistValue = preg_match('/id_path=\'([^\']*)\'/', $blockContentHtml, $pageBuilderLinkValue);
            if ($isExistValue) {
                $linkArray = explode('/', trim(end($pageBuilderLinkValue)));
                $result['link']['value'] = $linkArray[1];
                $result['link']['type'] = $linkArray[0];
            }
        } else {
            $isExistUrl = preg_match('/href=\"([^"]*)"/', $blockContentHtml, $urlLink);
            if ($isExistUrl) {
                $result['link']['value'] = end($urlLink) ?? null;
                $result['link']['type'] = 'url';
            }
        }

        return [
            'searchResult' => $result
        ];
    }
}
