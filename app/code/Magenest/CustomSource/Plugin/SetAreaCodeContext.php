<?php

namespace Magenest\CustomSource\Plugin;

use Magenest\CustomSource\Helper\Data;
use Magenest\Directory\Block\Adminhtml\Area\Field\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Stdlib\Cookie\CookieReaderInterface;

/**
 * Class SetAreaCodeContext
 */
class SetAreaCodeContext
{
    const AREA_CODE_CONTEXT = Area::COLUMN_AREA_CODE;

    /** @var HttpContext */
    protected $httpContext;

    /**
     * @var CookieReaderInterface
     */
    private $cookieReader;

    /**
     * @var Data
     */
    protected $data;

    /**
     * SetAreaCodeContext constructor.
     * @param CookieReaderInterface $cookieReader
     * @param HttpContext $httpContext
     */
    public function __construct(
        CookieReaderInterface $cookieReader,
        HttpContext $httpContext,
        Data $data
    ) {
        $this->httpContext = $httpContext;
        $this->cookieReader = $cookieReader;
        $this->data = $data;
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeDispatch()
    {
        $areaCode = $this->cookieReader->getCookie('area_code');
        $this->httpContext->setValue(
            self::AREA_CODE_CONTEXT,
            $areaCode,
            false
        );
    }
}
