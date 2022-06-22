<?php
/**
 * Magenest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magenest.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magenest.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magenest
 * @package     Magenest_StoreCredit
 * @copyright   Copyright (c) Magenest (https://www.magenest.com/)
 * @license     https://www.magenest.com/LICENSE.txt
 */

namespace Magenest\StoreCredit\Console\Command;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleResource;
use Magento\Store\Model\StoreManagerInterface;
use Magenest\StoreCredit\Model\Product\Type\StoreCredit;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Uninstall
 * @package Magenest\StoreCredit\Console\Command
 */
class Uninstall extends Command
{
    /**
     * @var ModuleResource
     */
    protected $moduleResource;

    /**
     * @var AttributeFactory
     */
    protected $attributeFactory;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $eavAttrCodes = [
        'allow_credit_range',
        'min_credit',
        'max_credit',
        'credit_amount',
        'credit_rate'
    ];

    /**
     * Uninstall constructor.
     *
     * @param ModuleResource $moduleResource
     * @param AttributeFactory $attributeFactory
     * @param CollectionFactory $collectionFactory
     * @param State $state
     * @param StoreManagerInterface $storeManager
     * @param null $name
     */
    public function __construct(
        ModuleResource $moduleResource,
        AttributeFactory $attributeFactory,
        CollectionFactory $collectionFactory,
        State $state,
        StoreManagerInterface $storeManager,
        $name = null
    ) {
        $this->moduleResource = $moduleResource;
        $this->attributeFactory = $attributeFactory;
        $this->collectionFactory = $collectionFactory;
        $this->state = $state;
        $this->storeManager = $storeManager;

        parent::__construct($name);
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('mpstorecredit:uninstall')->setDescription('Prepare to remove Magenest_StoreCredit module');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     * @throws LocalizedException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->state->setAreaCode('adminhtml');
        try {
            $productCollection = $this->collectionFactory->create()
                ->addFieldToFilter('type_id', StoreCredit::TYPE_STORE_CREDIT);
            /** @var Product $product */
            foreach ($productCollection as $product) {
                $stores = $this->storeManager->getStores();
                foreach ($stores as $store) {
                    $product->addAttributeUpdate('visibility', 1, $store->getId());
                }
            }
            $this->attributeFactory->create()->getCollection()->addFieldToFilter('entity_type_id', 4)
                ->addFieldToFilter('attribute_code', ['in' => $this->eavAttrCodes])->walk('delete');
            $this->moduleResource->getConnection()
                ->delete($this->moduleResource->getMainTable(), "module='Magenest_StoreCredit'");
            $output->writeln('<info>Prepare remove Magenest_StoreCredit module successfully</info>');
        } catch (Exception $e) {
            $output->writeln("<error>{$e->getMessage()}</error>");
        }
    }
}
