<?php
namespace Magenest\QuantitySold\Block\Product;

use Magenest\QuantitySold\Setup\Patch\Data\AddSoldQuantityAttribute;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class SoldQuantity extends AbstractView implements IdentityInterface
{
    const ENABLE       = "sold_quantity/general/enable";
    const INITIAL_SOLD = "sold_quantity/general/initial_sold";

    /** @var SerializerInterface */
    private $json;

    /** @var ScopeConfigInterface */
    private $scopeConfig;

    /** @var StoreManagerInterface */
    private $storeManager;

    private $totalQty = 0;

    /**
     * SoldQuantity constructor.
     *
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param SerializerInterface $json
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Context               $context,
        ArrayUtils            $arrayUtils,
        SerializerInterface   $json,
        ScopeConfigInterface  $scopeConfig,
        StoreManagerInterface $storeManager,
        array                 $data = []
    ) {
        $this->json         = $json;
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context, $arrayUtils, $data);
    }

    /**
     * @return bool|string
     */
    public function getQuantities()
    {
        $result         = [];
        $currentProduct = $this->getProduct();

        if ($currentProduct->getTypeId() === Configurable::TYPE_CODE) {
            $children = $currentProduct->getTypeInstance()->getUsedProducts($currentProduct);
            foreach ($children as $child) {
                $qty                     = $this->getProductSoldQuantity($child);
                $result[$child->getId()] = $this->numberPrefixEncode($qty);
                $this->totalQty          += $qty;
            }
        } else {
            $result = $this->numberPrefixEncode($this->getProductSoldQuantity($currentProduct));
        }

        return $this->json->serialize($result);
    }

    public function getTotalQty()
    {
        return $this->totalQty;
    }

    public function getProductSoldQuantity($product)
    {
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $finalSoldQty = 0;
            $children     = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($children as $child) {
                $finalSoldQty += $this->getProductSoldQuantity($child);
            }
        } else {
            $finalSoldQty = (int)$product->getData(AddSoldQuantityAttribute::SOLD_QTY);
            $buff         = 0;
            if ($product->getData(AddSoldQuantityAttribute::UTILIZE_INITIAL_SOLD_QTY) == Status::STATUS_ENABLED) {
                try {
                    $buff = (int)$this->scopeConfig->getValue(SoldQuantity::INITIAL_SOLD);
                } catch (\Exception $exception) {
                }
            }
            $finalSoldQty += $buff;
        }

        return $finalSoldQty;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isEnabled()
    {
        $websiteId = $this->_storeManager->getWebsite()->getId();
        return $this->_scopeConfig->isSetFlag(self::ENABLE, ScopeInterface::SCOPE_WEBSITE, $websiteId);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->getIdentities();
        }
        return [];
    }

    /**
     * @param $number
     *
     * @return string
     */
    public static function numberPrefixEncode($number)
    {
        $count = 0;
        $val   = $number;
        while (true) {
            if ($val / 1000 >= 1) {
                $val = $val / 1000;
                $count++;
            } else {
                break;
            }
        }
        $val = $res = round($val, 1);
        switch ($count) {
            case 1:
                $val .= "k";
                break;
            case 2:
                $val .= "tr";
                break;
            case 3:
                $val .= "B";
                break;
            case 4:
                $val .= "T";
                break;
            default:
                $val = $number;
                $count = 0;
        }

        if ($count != 0 && $number % ($res * $count * 1000) > 0) {
            $val .= "+";
        }

        return $val;
    }
}
