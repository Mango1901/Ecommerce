<?php


namespace Magenest\Affiliate\Block;

use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Helper\Data as DataHelper;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\AffiliateCatalogRule\Helper\Constant;
use Magenest\Customer\Block\AbstractNavigation;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Navigation
 * @package Magenest\Affiliate\Block
 */
class AffiliateNavigation extends AbstractNavigation
{
    const ALLOW_BOTH = 'both';
    const ALLOW_GUEST = 'guess';
    const ALLOW_LOGIN = 'login';

    /**
     * Search redundant /index and / in url
     */
    const REGEX_URL_PATTERN = '/affiliate/';

    /** @var HttpContext */
    protected $httpContext;

    /**
     * @var DataHelper
     */
    protected $dataHelper;

    public function __construct(
        Context $context,
        Data $helper,
        DataHelper $dataHelper,
        HttpContext $httpContext,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        $this->httpContext = $httpContext;
        parent::__construct($context, $helper, $data);
    }

    /**
     * @inheritDoc
     */
    protected function additionalClass()
    {
        return 'affiliate';
    }

    /**
     * @inheritDoc
     */
    public function getLinks()
    {
        $affiliateContext = $this->httpContext->getValue(Constant::IS_AFFILIATE_CONTEXT);
        $links = parent::getLinks();
        $affiliateAccount = $this->dataHelper->getCurrentAffiliate();
        foreach ($links as $index => $link) {
            if ($link->getActive() === self::ALLOW_GUEST && $affiliateContext) {
                unset($links[$index]);
            }

            if ($link->getActive() === self::ALLOW_LOGIN && $link->getCode() != 'refer' && !$affiliateContext) {
                unset($links[$index]);
            }

            if ($link->getCode() == "signup" && $affiliateAccount->getStatus() == Status::NEED_APPROVED) {
                unset($links[$index]);
            }
        }
        return $links;
    }
}
