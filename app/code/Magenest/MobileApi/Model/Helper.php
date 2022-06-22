<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\MobileApi\Model;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Catalog\Helper\Image as CatalogImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\View\Asset\ImageFactory as AssetImageFactory;
use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\View\ConfigInterface;
use Magento\Framework\Webapi\Rest\Request as RestRequest;

class Helper
{
    /** @const - Header r request */
    const DETAILS_HEADER    = 'X-Requested-Details';
    const STORE_CODE_HEADER = 'X-Requested-Store';
    /** X-Requested-With: XMLHttpRequest */

    /**
     * @var ConfigInterface
     */
    protected $_presentationConfig;

    /**
     * @var ParamsBuilder
     */
    protected $_imageParamsBuilder;

    /**
     * @var PlaceholderFactory
     */
    protected $_viewAssetPlaceholderFactory;

    /**
     * @var AssetImageFactory
     */
    protected $_viewAssetImageFactory;

    /**
     * @var State
     */
    protected $_appState;

    /**
     * @var RestRequest
     */
    protected $_restRequest;

    /**
     * @var UserContextInterface
     */
    protected $_userContext;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * Constructor.
     *
     * @param ConfigInterface $presentationConfig
     * @param ParamsBuilder $imageParamsBuilder
     * @param AssetImageFactory $viewAssetImageFactory
     * @param PlaceholderFactory $viewAssetPlaceholderFactory
     * @param State $appState
     * @param RestRequest $restRequest
     * @param UserContextInterface $userContext
     * @param CustomerSession $customerSession
     */
    public function __construct(
        ConfigInterface      $presentationConfig,
        ParamsBuilder        $imageParamsBuilder,
        AssetImageFactory    $viewAssetImageFactory,
        PlaceholderFactory   $viewAssetPlaceholderFactory,
        State                $appState,
        RestRequest          $restRequest,
        UserContextInterface $userContext,
        CustomerSession      $customerSession
    ) {
        $this->_presentationConfig          = $presentationConfig;
        $this->_imageParamsBuilder          = $imageParamsBuilder;
        $this->_viewAssetPlaceholderFactory = $viewAssetPlaceholderFactory;
        $this->_viewAssetImageFactory       = $viewAssetImageFactory;
        $this->_appState                    = $appState;
        $this->_restRequest                 = $restRequest;
        $this->_userContext                 = $userContext;
        $this->_customerSession             = $customerSession;
    }

    /**
     * Initial session
     */
    public function initialSession()
    {
        if ($this->_userContext->getUserId()
            && $this->_userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER) {
            $this->_customerSession->setCustomerId($this->_userContext->getUserId());
        }
    }

    /**
     * Is rest api get
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isRestApiGet()
    {
        return $this->_restRequest->isGet() && ($this->_appState->getAreaCode() == 'webapi_rest');
    }

    /**
     * Is detail request
     * @return bool|string
     */
    public function isDetailRequest()
    {
        return filter_var($this->_restRequest->getHeader(Helper::DETAILS_HEADER), FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * @param Product $product
     * @param $imageId
     *
     * @return string
     */
    public function getImageUrl(Product $product, $imageId)
    {
        try {
            $viewImageConfig = $this->_presentationConfig->getViewConfig(['area' => Area::AREA_FRONTEND])
                ->getMediaAttributes(
                    'Magento_Catalog',
                    CatalogImageHelper::MEDIA_TYPE_CONFIG_NODE,
                    $imageId
                );

            $imageMiscParams  = $this->_imageParamsBuilder->build($viewImageConfig);
            $originalFilePath = $product->getData($imageMiscParams['image_type']);

            if (!$originalFilePath) {
                $originalFilePath = 'placeholder/image.jpg';
            }

            $imageAsset = $this->_viewAssetImageFactory->create(
                ['miscParams' => $imageMiscParams, 'filePath' => $originalFilePath,]
            );

            return $imageAsset->getUrl();
        } catch (\Exception $e) {
            return "";
        }
    }
}
