<?php

namespace Magenest\PhotoReview\Block\Review;

use Exception;
use Magenest\PhotoReview\Helper\Data;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Url;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\File\Size;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Review\Model\RatingFactory;

class Form extends \Magento\Review\Block\Form
{
    /** @var Size */
    protected $_fileSize;

    /** @var Data */
    protected $_photoHelper;

    public function __construct(
        Size                                             $fileSize,
        Data                                             $photoHelper,
        \Magento\Framework\View\Element\Template\Context $context,
        EncoderInterface                                 $urlEncoder,
        \Magento\Review\Helper\Data                      $reviewData,
        ProductRepositoryInterface                       $productRepository,
        RatingFactory                                    $ratingFactory,
        ManagerInterface                                 $messageManager,
        \Magento\Framework\App\Http\Context              $httpContext,
        Url                                              $customerUrl,
        Json                                             $serializer = null,
        array                                            $data = []
    ) {
        $this->_fileSize = $fileSize;
        $this->_photoHelper = $photoHelper;
        $this->serializer = $serializer ?: ObjectManager::getInstance()->get(Json::class);

        parent::__construct(
            $context,
            $urlEncoder,
            $reviewData,
            $productRepository,
            $ratingFactory,
            $messageManager,
            $httpContext,
            $customerUrl,
            $data,
            $serializer
        );
    }
    protected function _construct()
    {
        parent::_construct();

        $this->setAllowWriteReviewFlag(
            $this->httpContext->getValue(Context::CONTEXT_AUTH)
            || $this->_reviewData->getIsGuestAllowToWrite()
        );
        if (!$this->getAllowWriteReviewFlag()) {
            $queryParam = $this->urlEncoder->encode(
                $this->getUrl('*/*/*', ['_current' => true]) . '#review-form'
            );
            $this->setLoginLink(
                $this->getUrl(
                    'customer/account/login/',
                    [Url::REFERER_QUERY_PARAM_NAME => $queryParam]
                )
            );
        }
        if($this->_photoHelper->isModuleEnable()){
            $this->setTemplate('Magenest_PhotoReview::review/form.phtml');
        }else{
            $this->setTemplate('Magento_Review::form.phtml');
        }
    }

    /**
     * @return bool
     */
    public function isEnableProsCons()
    {
        return (boolean)($this->_photoHelper->isEnableProsCons());
    }

    /**
     * @return bool
     */
    public function canAddExternalLinks()
    {
        return (boolean)($this->_photoHelper->getScopeConfig(Data::ENABLE_ADD_EXTERRAL_LINKS));
    }

    /**
     * @return int
     */
    public function maxPhotoNumber()
    {
        $number = $this->_photoHelper->getScopeConfig(Data::MAX_PHOTO_UPLOAD);
        return (int)($number != null ? $number : 1);
    }

    /**
     * @return bool
     */
    public function isRequiredPhoto()
    {
        $required = $this->_photoHelper->getScopeConfig(Data::REQUIRED_PHOTO);
        return (boolean)($required);
    }

    /**
     * @return float
     */
    public function getMaxUploadSize()
    {
        $maxImageSize = $this->_fileSize->getMaxFileSizeInMb();
        return $maxImageSize * 1048576;
    }

    /**
     * @return int
     */
    public function maxVideoNumber(){
        $number = $this->_photoHelper->getScopeConfig(\Magenest\PhotoReview\Helper\Data::MAX_VIDEO_UPLOAD);
        return (int)($number != null ? $number : 1);
    }

    /**
     * @return float
     */
    public function getMaxUploadVideoSize()
    {
        $maxVideoSize = $this->_photoHelper->getScopeConfig(\Magenest\PhotoReview\Helper\Data::MAX_VIDEO_SIZE_UPLOAD);
        return $maxVideoSize*1048576;
    }


    /**
     * @return bool
     */
    public function isRequiredVideo(){
        $required = $this->_photoHelper->getScopeConfig(\Magenest\PhotoReview\Helper\Data::REQUIRED_VIDEO);
        return (boolean)($required);
    }


    /**
     * @return array
     */
    public function getLabelSummaryOptions()
    {
        return $this->_photoHelper->getLabelSummary();
    }
}
