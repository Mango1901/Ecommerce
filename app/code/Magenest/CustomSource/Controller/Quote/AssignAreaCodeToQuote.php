<?php

namespace Magenest\CustomSource\Controller\Quote;

use Exception;
use Magenest\CustomSource\Model\Source\Area\Options;
use Magenest\CustomSource\Plugin\SetAreaCodeContext;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;

class AssignAreaCodeToQuote extends Action
{
    const COOKIE_NAME_CODE = 'area_code';

    const COOKIE_NAME_LABEL = 'area_label';

    const COOKIE_DURATION = 86400; // lifetime in seconds

    /**
     * @var JsonFactory
     */
    protected $jsonResultFactory;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepositoryInterface;

    /**
     * @var Session
     */
    protected $_checkoutSession;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Options
     */
    protected $areaOptions;

    /** @var HttpContext */
    protected $httpContext;

    /**
     * @var CartInterface
     */
    protected $cart;

    /**
     * @var CookieManagerInterface
     */
    protected $cookieManager;

    /**
     * @var CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * @param JsonFactory $jsonResultFactory
     * @param CartRepositoryInterface $quoteRepositoryInterface
     * @param Session $checkoutSession
     * @param LoggerInterface $logger
     * @param Options $areaOptions
     * @param HttpContext $httpContext
     * @param Context $context
     * @param CartInterface $cart
     * @param CookieManagerInterface $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        JsonFactory             $jsonResultFactory,
        CartRepositoryInterface $quoteRepositoryInterface,
        Session                 $checkoutSession,
        LoggerInterface         $logger,
        Options                 $areaOptions,
        HttpContext             $httpContext,
        Context                 $context,
        CartInterface           $cart,
        CookieManagerInterface  $cookieManager,
        CookieMetadataFactory   $cookieMetadataFactory
    ) {
        $this->jsonResultFactory = $jsonResultFactory;
        $this->quoteRepositoryInterface = $quoteRepositoryInterface;
        $this->_checkoutSession = $checkoutSession;
        $this->logger = $logger;
        $this->areaOptions = $areaOptions;
        $this->httpContext = $httpContext;
        $this->cart = $cart;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Json|ResultInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute()
    {
        $result = $this->jsonResultFactory->create();
        $data = ['success' => false];
        try {
            $areaCode = $this->getRequest()->getPost('area_code');
            if ($areaCode && $this->checkValidAreaCode($areaCode)) {
                $quote = $this->_checkoutSession->getQuote();
                $quote->setAreaCode($areaCode);

                $quote->removeAllItems();

                $this->quoteRepositoryInterface->save($quote);

                $data['success'] = true;
                $this->httpContext->setValue(
                    SetAreaCodeContext::AREA_CODE_CONTEXT,
                    $areaCode,
                    false
                );
            } else {
                $this->messageManager->addWarningMessage(__('Area does not exists'));
            }
        } catch (Exception $exception) {
            $this->logger->error($exception->getMessage());
        }
        $result->setData($data);
        return $result;
    }

    /**
     * @param string $areaCode
     * @return bool
     */
    private function checkValidAreaCode($areaCode)
    {
        foreach ($this->areaOptions->toOptionArray() as $item) {
            if ($item['value'] == $areaCode) {
                $this->setPublicCookieArea(self::COOKIE_NAME_CODE, $item['value']);
                $this->setPublicCookieArea(self::COOKIE_NAME_LABEL, $item['label']);
                return true;
            }
        }
        return false;
    }

    /**
     * Set public cookie
     */
    private function setPublicCookieArea($cookieName, $value)
    {
        $metadata = $this->cookieMetadataFactory
            ->createPublicCookieMetadata()
            ->setDuration(self::COOKIE_DURATION)
            ->setPath('/')
            ->setHttpOnly(false); // JS-accessible

        $this->cookieManager->deleteCookie($cookieName, $metadata);
        $this->cookieManager->setPublicCookie(
            $cookieName,
            $value,
            $metadata
        );
    }
}
