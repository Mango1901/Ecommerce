<?php
namespace Magenest\MobileApi\Model\Resolver\Slider\Query;

use Magenest\MobileApi\Setup\Patch\Data\SliderHomePage;
use Magento\Cms\Model\ResourceModel\Block\CollectionFactory as CmsCollection;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Store\Model\StoreManagerInterface;

class SliderQuery implements SliderQueryInterface
{
    /** @var CmsCollection */
    protected $blockCollectionFactory;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public function __construct(
        CmsCollection $blockCollectionFactory,
        StoreManagerInterface $storeManager,
        SerializerInterface $serializer
    ) {
        $this->blockCollectionFactory = $blockCollectionFactory;
        $this->storeManager = $storeManager;
        $this->serializer = $serializer;
    }

    public function getResult($args, $info, $context)
    {
        $identifier = SliderHomePage::SLIDER_HOME_PAGE_MOBILE;

        $block = $this->blockCollectionFactory->create()
            ->addFieldToFilter(
                'identifier',
                $identifier
            )
            ->setCurPage(1)
            ->setPageSize(1)
            ->getFirstItem();

        $blockContentHtml = $block->getContent();

        $result = [];

        $sliderRegex = '/<div data-content-type=\"slide\"((.|\n)*?)<\/div>/';
        $backgroundDesktopImageRegex = '/data-background-images=\"([^"]*)\"/';
        $linkRegex = '/{{widget(.[^>]+)/';

        $isExistsColumn = preg_match_all($sliderRegex, $blockContentHtml, $pageBuilderSliders);
        if ($isExistsColumn) {
            foreach ($pageBuilderSliders[0] as $index => $item) {
                $isExistsBackgroundImage = preg_match($backgroundDesktopImageRegex, $item, $pageBuilderBackgroundImages);
                if ($isExistsBackgroundImage) {
                    $imageData = htmlspecialchars_decode(end($pageBuilderBackgroundImages));

                    $isExistDesktopImage = preg_match('/\\\\\"desktop_image\\\\\":\\\\"([^"]*)\\\\\"/m', $imageData, $pageBuilderDesktopImage);;
                    if ($isExistDesktopImage) {
                        $result[$index]['background_image'] = end($pageBuilderDesktopImage);
                    }

                    $isExistMobileImage = preg_match('/\\\\\"mobile_image\\\\\":\\\\"([^"]*)\\\\\"/m', $imageData, $pageBuilderMobileImage);
                    if ($isExistMobileImage) {
                        $result[$index]['background_mobile_image'] = end($pageBuilderMobileImage);
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
            'searchResult' => $result,
            'block_id' => $block->getId(),
            'identifier' => $block->getIdentifier()
        ];
    }
}
