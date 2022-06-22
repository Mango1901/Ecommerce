<?php
/**
 * Copyright © 2018 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_RewardPoints extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_RewardPoints
 */

namespace Magenest\RewardPoints\Setup;

use Magento\Cms\Model\PageFactory;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Sales\Setup\SalesSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    const NAMESPACE_REWARDPOINTS_ACCOUNT_LISTING = 'rewardpoints_account_listing';

    const NAMESPACE_REWARDPOINTS_TRANSACTION_LISTING = 'rewardpoints_transaction_listing';

    /**
     * @var PageFactory
     */
    private $pageFactory;

    /**
     * @var \Magenest\RewardPoints\Setup\RewardPointSetupFactory
     */
    private $rewardPointSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @var \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory
     */
    protected $bookmarkCollectionFactory;

    /**
     * @var \Magento\Framework\View\Element\Template
     */
    protected $template;

    /**
     * UpgradeData constructor.
     * @param RewardPointSetupFactory $rewardPointSetupFactory
     * @param PageFactory $pageFactory
     * @param \Magento\Framework\App\State $state
     * @param SalesSetupFactory $salesSetupFactory
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $bookmarkCollectionFactory
     * @param \Magento\Framework\View\Element\Template $template
     */
    public function __construct(
        RewardPointSetupFactory $rewardPointSetupFactory,
        PageFactory $pageFactory,
        \Magento\Framework\App\State $state,
        SalesSetupFactory $salesSetupFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Ui\Model\ResourceModel\Bookmark\CollectionFactory $bookmarkCollectionFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->resource = $resource;
        $this->pageFactory = $pageFactory;
        $this->rewardPointSetupFactory = $rewardPointSetupFactory;
        $this->state = $state;
        $this->bookmarkCollectionFactory = $bookmarkCollectionFactory;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->state->emulateAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL,
            function ($setup, $context) {
                $setup->startSetup();
                if (version_compare($context->getVersion(), '1.0.7') < 0) {
                    $this->addRewardPointAttributes($setup);
                }
                if (version_compare($context->getVersion(), '1.0.8') < 0) {
                    $this->addLandingPage();
                }
                if (version_compare($context->getVersion(), '1.0.8') < 0) {
                    $this->addRefundByPointAttributes($setup);
                }
                if (version_compare($context->getVersion(), '1.1.0') < 0) {
                    $this->clearBookmark($setup);
                }
                $setup->endSetup();
            },
            [$setup, $context]
        );
    }

    private function addRewardPointAttributes($setup)
    {
        $rewardPointSetup = $this->rewardPointSetupFactory->create(['setup' => $setup]);
        $attributes = [
            'reward_tier' => ['type' => Table::TYPE_DECIMAL],
        ];
        foreach ($attributes as $attributeCode => $attributeParams) {
            $rewardPointSetup->addAttribute('quote', $attributeCode, $attributeParams);

            $rewardPointSetup->addAttribute('quote_address', $attributeCode, $attributeParams);

            $rewardPointSetup->addAttribute('order', $attributeCode, $attributeParams);

            //perhaps invoice and creditmemo do not need hold data of reward point
            $rewardPointSetup->addAttribute('invoice', $attributeCode, $attributeParams);
            $rewardPointSetup->addAttribute('creditmemo', $attributeCode, $attributeParams);
        }
    }

    private function addRefundByPointAttributes($setup)
    {
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $salesInstaller->addAttribute('creditmemo', 'bs_customer_mgn_rwp_total_refunded', ['type' => 'decimal']);
        $salesInstaller->addAttribute('creditmemo', 'customer_mgn_rwp_total_refunded', ['type' => 'decimal']);

        $salesInstaller->addAttribute('order', 'bs_customer_mgn_rwp_total_refunded', ['type' => 'decimal']);
        $salesInstaller->addAttribute('order', 'customer_mgn_rwp_total_refunded', ['type' => 'decimal']);

        $this->resource->getConnection('sales_order')->addColumn(
            $setup->getTable('sales_order_grid'),
            'refunded_to_mgn_rwp',
            [
                'TYPE' => Table::TYPE_DECIMAL,
                'LENGTH' => '12,4',
                'COMMENT' => 'Refund to Reward Points'
            ]
        );
    }

    private function addLandingPage()
    {
        $cmsPageData =
            [
                'is_active' => 1,
                'title' => __("Reward Points"),
                'content_heading' => 'Reward Points',
                'content' => $this->getLandingPageHtml(),
                'identifier' => 'reward-landing',
                'page_layout' => '1column',
                'stores' => [0],
                'sort_order' => 0
            ];

        $pageModel = $this->pageFactory->create();
        if (isset($cmsPageData)) {
            $id = $pageModel->getResource()->checkIdentifier($cmsPageData['identifier'], 0);
            if ($id) {
                $pageModel->load($id)->addData($cmsPageData)->save();
            } else {
                $pageModel->setData($cmsPageData)->save();
            }
        }
    }

    private function getLandingPageHtml()
    {
        $html = '';

        $html .= '
        <div class="reward-wrapper">
        <div class="hero">
            <h4>Our Loyalty Program</h4>
            <h2>Everything you</h2>
            <h2>need to know</h2>
        </div>
        <div class="benefit">
            <div class="summary">
                <h3 class="summary-title">Your Loyalty Program Benefits</h3>
                <p class="summary-content">Your Loyalty Program Benefits Great news: Each time you shop with us, you automatically get rewarded! The minute you create an account, you can start earning Points to spend on your next purchases. Even better news: You’re on your way to bigger perks. The more you shop, the closer you’ll be to qualifying for our VIP tier, which gives you access to exclusive added benefits.</p>
            </div>
            <div class="tier">
                <div class="tier-1">
                    <h4 class="tier-title">Tier 1</h4>
                    <p class="tier-content">Earn 1 Point for every $1 you spend by shopping and gifting subscriptions (that’s 10% back on purchases!)</p>
                </div>
                <div class="tier-2">
                    <h4 class="tier-title">Tier 2</h4>
                    <p class="tier-content">Earn 1 Point for every $1 you spend by shopping and gifting subscriptions (that’s 10% back on purchases!)</p>
                </div>
                <div class="tier-3">
                    <h4 class="tier-title">Tier 3</h4>
                    <p class="tier-content">Earn 1 Point for every $1 you spend by shopping and gifting subscriptions (that’s 10% back on purchases!)</p>
                </div>
            </div>
        </div>
        <div class="how-it-works">
            <div class="title">
                How our Loyalty Program Points work
            </div>
            <p>We make it easy for you to quickly rack up redeemable Loyalty Points. From the very start, you’ll automatically earn one point for every purchase.</p>
            <div class="top-image"></div>
            <div class="term-and-condition">
                <a href="#">Terms & Conditions</a>
            </div>
            <div class="what-it-worth">
                <div>
                    <strong>
                        What your Loyalty Points are worth: 
                    </strong>
                    <p>Points can be redeemed as a payment toward any purchase in the store</p>
                </div>
            </div>
            <div class="bottom-image"></div>
        </div>
        <div class="faq">
            <h3>F.A.Q</h3>
            <div class="question">
                <strong>How do I participate in the Loyalty Program ?</strong>
                <p>You are part of the Loyalty Program from the moment you create an account with us, you will begin to earn points with every purchase you make on out store</p>
            </div>
            <div class="question">
                <strong>How do I spend Loyalty Program Points ?</strong>
                <p>Your Loyalty Points can be redeemed toward orders at any time. Your Loyalty Points will be available as an option to include at checkout. You can choose how many points you want to use with each order.</p>
            </div>
            <div class="question">
                <strong>Do my Points expire ?</strong>
                <p>No they do not. Cheers!</p>
            </div>
        </div>
        </div>';

        return $html;
    }

    /**
     * Clear book mark for Points Manager and Transaction History Manager grid
     */
    private function clearBookmark()
    {
        $bookmarkCollection = $this->bookmarkCollectionFactory->create()
            ->addFieldToFilter('namespace', ['in' => [self::NAMESPACE_REWARDPOINTS_ACCOUNT_LISTING, self::NAMESPACE_REWARDPOINTS_TRANSACTION_LISTING]]);
        if ($bookmarkCollection->getSize()) {
            foreach ($bookmarkCollection as $bookmark) {
                $bookmark->delete();
            }
        }
    }

}
