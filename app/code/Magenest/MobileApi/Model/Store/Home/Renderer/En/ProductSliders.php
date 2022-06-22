<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model\Store\Home\Renderer\En;

use Magenest\MobileApi\Model\Store\Home\Renderer\DefaultInterface;
use Magenest\MobileApi\Api\Data\Store\HomeExtension;
use Magenest\MobileApi\Model\Catalog\Widget\ProductFactory as ProductWidgetFactory;
use Magenest\MobileApi\Model\Catalog\Widget\BestsellersFactory;
use Magenest\MobileApi\Model\Catalog\Widget\NewProductFactory;
use Magenest\MobileApi\Model\Catalog\Widget\SalesTodayFactory;
use Magenest\MobileApi\Model\Catalog\Widget\MaybeInterestedFactory;
use Magento\Framework\UrlInterface;

/**
 * Class Promotion
 * @package Magenest\MobileApi\Model\Store\Home\Renderer\En
 */
class ProductSliders implements DefaultInterface
{
    /**
     * @var BestsellersFactory
     */
    protected $_bestsellersFactory;

    /**
     * @var NewProductFactory
     */
    protected $_newProductFactory;

    /**
     * @var SalesTodayFactory
     */
    protected $_saleTodayFactory;

    /**
     * @var MaybeInterestedFactory
     */
    protected $_maybeInterested;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Constructor.
     *
     * @param NewProductFactory $newProductFactory
     * @param BestsellersFactory $bestsellersFactory
     * @param SalesTodayFactory $salesTodayFactory
     * @param MaybeInterestedFactory $maybeInterested
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        NewProductFactory $newProductFactory,
        BestsellersFactory $bestsellersFactory,
        SalesTodayFactory $salesTodayFactory,
        MaybeInterestedFactory $maybeInterested,
        UrlInterface $urlInterface
    )
    {
        $this->_bestsellersFactory = $bestsellersFactory;
        $this->_newProductFactory = $newProductFactory;
        $this->_saleTodayFactory = $salesTodayFactory;
        $this->_maybeInterested = $maybeInterested;
        $this->_urlBuilder = $urlInterface;
    }

    /**
     * @inheritdoc
     */
    public function process(HomeExtension $extension)
    {
        /** Bestseller */
        $extension->setBestseller($this->getHomeWidgetBestSeller(10));
        /** New */
        $extension->setNew($this->getHomeWidgetNew(10));
        /** Sales Today */
        $extension->setSalesToday($this->getHomeWidgetSaleToday(10));
        /** Maybe Interested */
        $extension->setMaybeInterested($this->getHomeWidgetMaybeInterested(10));
    }

    /**
     * Get Home new widget
     *
     * @param int $limit
     * @param int $page
     * @return \Magenest\MobileApi\Model\Catalog\Widget\ProductSlider
     */
    public function getHomeWidgetNew($limit, $page = 1)
    {
        return $this->_newProductFactory->create([
            'data' => [
                'title' => __('New Arrival'),
                'show_pager' => false,
                'product_count' => $limit,
                'page' => $page,
            ]
        ])->render();
    }

    /**
     * Get home widget best seller
     *
     * @param int $limit
     * @param int $page
     * @return \Magenest\MobileApi\Model\Catalog\Widget\ProductSlider
     */
    public function getHomeWidgetBestseller($limit, $page = 1)
    {
        return $this->_bestsellersFactory->create([
            'data' => [
                'title' => __('Bestseller')
            ]
        ])->render();
    }

    /**
     * Get Home Sales Today widget
     *
     * @param int $limit
     * @param int $page
     * @return \Magenest\MobileApi\Model\Catalog\Widget\ProductSlider
     */
    public function getHomeWidgetSaleToday($limit, $page = 1)
    {
        return $this->_saleTodayFactory->create([
            'data' => [
                'title' => __('Sale Today'),
                'show_pager' => false,
                'product_count' => $limit,
                'page' => $page,
            ]
        ])->render();
    }

    /**
     * Get Home Maybe Interested widget
     *
     * @param int $limit
     * @param int $page
     * @return \Magenest\MobileApi\Model\Catalog\Widget\ProductSlider
     */
    public function getHomeWidgetMaybeInterested($limit, $page = 1)
    {
        return $this->_maybeInterested->create([
            'data' => [
                'title' => __('Maybe Interested'),
                'show_pager' => false,
                'product_count' => $limit,
                'page' => $page,
            ]
        ])->render();
    }
}
