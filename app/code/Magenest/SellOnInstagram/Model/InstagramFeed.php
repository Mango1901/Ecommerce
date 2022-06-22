<?php
namespace Magenest\SellOnInstagram\Model;

use Exception;
use Magento\Framework\Registry;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Data\Collection\AbstractDb;
use Magenest\SellOnInstagram\Model\Rule\GetValidProduct;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Serialize\Serializer\Json as JsonFramework;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeedIndex as InstagramFeedIndexResourceModel;

class InstagramFeed extends AbstractModel
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    const TABLE = 'magenest_instagram_feed';
    const REGISTER = 'feed_model';
    const REGISTER_SALE_RULE = 'current_promo_sale_rule';

    /**
     * @var string
     */
    protected $_eventPrefix = 'instagram_feed';
    /**
     * @var InstagramFeedIndexResourceModel
     */
    protected $instagramFeedIndex;
    /**
     * @var GetValidProduct
     */
    protected $ruleValidProduct;
    /**
     * @var JsonFramework
     */
    protected $jsonFramework;

    public function __construct(
        InstagramFeedIndexResourceModel $instagramFeedIndex,
        GetValidProduct $ruleValidProduct,
        JsonFramework $jsonFramework,
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->instagramFeedIndex = $instagramFeedIndex;
        $this->ruleValidProduct = $ruleValidProduct;
        $this->jsonFramework = $jsonFramework;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function _construct()
    {
        $this->_init(\Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed::class);
    }

    /**
     * @return InstagramFeed
     * @throws Exception
     */
    public function afterSave()
    {
        $this->generateProduct();

        return parent::afterSave();
    }

    /**
     * @throws Exception
     */
    public function generateProduct()
    {
        try {
            $productIds = $this->ruleValidProduct->execute($this);
            $data = [
                'feed_id' => $this->getId(),
                'template_id' => $this->getTemplateId(),
                'product_ids' => $this->jsonFramework->serialize($productIds)
            ];
            $this->instagramFeedIndex->insertData($data);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @return array
     */
    public function getProductIds()
    {
        return $this->getResource()->getProductIds($this->getId());
    }

    /**
     * @return void
     */
    public function syncByFeedId()
    {
        return $this->getResource()->syncByFeedId($this->getId());
    }
}
