<?php
namespace Magenest\SocialLogin\ViewModel;

use Magenest\AffiliateCatalogRule\Helper\Constant;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class AffiliateContext implements ArgumentInterface
{
    /** @var Context */
    private $httpContext;

    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param Context $httpContext
     * @param SerializerInterface $serializer
     */
    public function __construct(Context $httpContext, SerializerInterface $serializer)
    {
        $this->serializer  = $serializer;
        $this->httpContext = $httpContext;
    }

    /**
     * @return mixed|null
     */
    public function getContext()
    {
        return $this->httpContext->getValue(Constant::IS_AFFILIATE_CONTEXT);
    }

    /**
     * @param $object
     *
     * @return bool|string
     */
    public function jsonEncode($object)
    {
        return $this->serializer->serialize($object);
    }
}
