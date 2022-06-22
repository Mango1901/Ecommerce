<?php


namespace Magenest\Affiliate\Model;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class Banner
 * @package Magenest\Affiliate\Model
 */
class Banner extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'magenest_affiliate_banner';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'magenest_affiliate_banner';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_affiliate_banner';

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * Banner constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param FilterProvider $filterProvider
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FilterProvider $filterProvider,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;

        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\Affiliate\Model\ResourceModel\Banner');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getContentHtml()
    {
        return $this->filterProvider->getPageFilter()->filter($this->getContent());
    }
}
