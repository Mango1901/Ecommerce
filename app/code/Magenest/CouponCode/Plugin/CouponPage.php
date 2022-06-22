<?php

namespace Magenest\CouponCode\Plugin;

use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class CouponPage
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var NodeFactory
     */
    protected $nodeFactory;

    /**
     * Initialize dependencies.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Tree\NodeFactory $nodeFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        NodeFactory $nodeFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->nodeFactory = $nodeFactory;
    }

    /**
     * Inject node into menu.
     *
     * @param \Magento\Theme\Block\Html\Topmenu $subject
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function beforeGetHtml(
        \Magento\Theme\Block\Html\Topmenu $subject,
        $outermostClass = '',
        $childrenWrapClass = '',
        $limit = 0
    ) {
        $enable = $this->scopeConfig
            ->isSetFlag('magenest_coupon/eventmenu/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($enable) {
            $name = $this->scopeConfig
                ->getValue('magenest_coupon/eventmenu/event_name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $url = $this->storeManager->getStore()->getBaseUrl();
            $node = $this->nodeFactory->create(
                [
                    'data' => [
                        'name' => __($name),
                        'id' => 'coupon',
                        'url' => $url.'voucher/hunt/index',
                        'is_active' => false
                    ],
                    'idField' => 'coupon',
                    'tree' => $subject->getMenu()->getTree()
                ]
            );
            $subject->getMenu()->addChild($node);
        }
    }
}
