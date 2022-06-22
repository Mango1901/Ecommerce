<?php


namespace Magenest\CategoryIcon\Helper;


use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Asset\Repository;

/**
 * Class Data
 * @package Magenest\CategoryIcon\Helper
 */
class Data extends AbstractHelper
{
    protected $repository;
    protected $imageHlp;

    /**
     * Data constructor.
     * @param Repository $repository
     * @param Image $imageHlp
     * @param Context $context
     */
    public function __construct(
        Repository $repository,
        Image $imageHlp,
        Context $context
    )
    {
        $this->repository = $repository;
        $this->imageHlp   = $imageHlp;
        parent::__construct($context);
    }

    /**
     * @param Product $productObj
     * @return string
     */
    public function getImageUrlByProduct($productObj)
    {

        if ($productObj) {
            $image        = $productObj->getData('small_image') ?: $productObj->getData('thumbnail');
            $productImage = $this->imageHlp
                ->init($productObj, 'product_banner_image')
                ->setImageFile($image);
            if ($image) {
                $imageUrl = $productImage->getUrl();
            } else {
                $imageUrl = $this->repository->getUrl($this->imageHlp->getPlaceholder('small_image'));
            }
        } else {
            $imageUrl = $this->repository->getUrl($this->imageHlp->getPlaceholder('small_image'));
        }
        return $imageUrl;
    }

    /**
     * @param Product $productObj
     * @param $block
     * @return string
     */
    public function getPriceHtml($productObj, $block)
    {
        $priceRender = $this->getPriceRender($block);

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $productObj,
                [
                    'include_container' => true,
                    'display_minimal_price' => true,
                    'zone' => Render::ZONE_ITEM_LIST,
                    'list_category_page' => true
                ]
            );
        }

        return $price;
    }

    protected function getPriceRender($block)
    {
        return $block->getLayout()->getBlock('product.price.render.default')
            ->setData('is_product_list', true);
    }

    /**
     * @param Product $productObj
     * @return float|null
     */
    public function getDiscountPercent($productObj)
    {
        $productPrice      = $productObj->getPrice();
        $productFinalPrice = $productObj->getFinalPrice();
        if ($productPrice && $productFinalPrice && $productPrice != $productFinalPrice) {
            if ((int)$productPrice > (int)$productFinalPrice) {
                $discountPercent = 100 - ((int)$productFinalPrice / (int)$productPrice * 100);
                return round($discountPercent);
            }
        }
        return null;
    }


}
