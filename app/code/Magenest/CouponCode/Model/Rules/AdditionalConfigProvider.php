<?php

namespace Magenest\CouponCode\Model\Rules;

use Magenest\CouponCode\Helper\Data;
use Magenest\CouponCode\Model\Configurations;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Http\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Asset\Repository;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection;

class AdditionalConfigProvider implements ConfigProviderInterface
{
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var Repository
     */
    protected $asset;
    /**
     * @var Collection
     */
    protected $_collection;

    /**
     * @var Configurations
     */
    protected $_configurations;

    /**
     * @var Data
     */
    protected $_dataHelper;

    /**
     * AdditionalConfigProvider constructor.
     * @param Repository $asset
     * @param Collection $collection
     * @param Configurations $configurations
     * @param Data $dataHelper
     * @param Context $context
     */
    public function __construct(
        Repository     $asset,
        Collection     $collection,
        Configurations $configurations,
        Data           $dataHelper,
        Context        $context
    ) {
        $this->context = $context;
        $this->asset = $asset;
        $this->_configurations = $configurations;
        $this->_collection = $collection;
        $this->_dataHelper = $dataHelper;
    }

    /**
     * Get config
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getConfig()
    {
        $rules = $this->_collection->addCustomerGroupFilter($this->context->getValue('customer_group_id'));
        $enableCouponListing = $this->_dataHelper->getEnableCouponListingConfiguration();
        return [
            'rules' => $rules->getData(),
            'enable' => $enableCouponListing,
            'default_image' => $this->asset->getUrl('Magenest_CouponCode::image/default-coupon.png')
        ];
    }
}
