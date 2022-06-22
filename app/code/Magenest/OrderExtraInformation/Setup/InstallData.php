<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderExtraInformation\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;

/**
 * Class InstallData
 * @package Magenest\OrderExtraInformation\Setup
 */
class InstallData implements InstallDataInterface
{
	/**
	 * @var SalesSetupFactory
	 */
	protected $_salesSetupFactory;

	/**
	 * @var QuoteSetupFactory
	 */
	protected $_quoteSetupFactory;

	/**
	 * @var CustomerSetupFactory $customerSetupFactory
	 */
	protected $_customerSetupFactory;

	/**
	 * @var SetFactory $attributeSetFactory
	 */
	protected $_attributeSetFactory;

	/**
	 * Constructor.
	 *
	 * @param SalesSetupFactory $salesSetupFactory
	 * @param QuoteSetupFactory $quoteSetupFactory
	 * @param CustomerSetupFactory $customerSetupFactory
	 * @param SetFactory $attributeSetFactory
	 */
	public function __construct(
		SalesSetupFactory $salesSetupFactory,
		QuoteSetupFactory $quoteSetupFactory,
		CustomerSetupFactory $customerSetupFactory,
		SetFactory $attributeSetFactory
	)
	{
		$this->_salesSetupFactory    = $salesSetupFactory;
		$this->_quoteSetupFactory    = $quoteSetupFactory;
		$this->_customerSetupFactory = $customerSetupFactory;
		$this->_attributeSetFactory = $attributeSetFactory;
	}

	/**
	 * {@inheritdoc}
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$installer = $setup;
		$installer->startSetup();

		/** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
		$salesInstaller = $this->_salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);
		/** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
		$quoteInstaller = $this->_quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
		/** @var CustomerSetup $customerSetup */
		$customerSetup = $this->_customerSetupFactory->create(['setup' => $setup]);
		$customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
		$attributeSetId = $customerEntity->getDefaultAttributeSetId();
		$attributeSet = $this->_attributeSetFactory->create();
		$attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

		$salesInstaller->addAttribute('order', 'company_name', ['type' => 'varchar']);
		$salesInstaller->addAttribute('order', 'tax_code', ['type' => 'varchar']);
		$salesInstaller->addAttribute('order', 'company_address', ['type' => 'varchar']);
		$salesInstaller->addAttribute('order', 'customer_note', ['type' => 'text']);
		$salesInstaller->addAttribute('order', 'delivery_date', ['type' => 'text']);
		$salesInstaller->addAttribute('order_item', 'customer_note', ['type' => 'text']);

		$quoteInstaller->addAttribute('quote', 'company_name', ['type' => 'text']);
		$quoteInstaller->addAttribute('quote', 'tax_code', ['type' => 'text']);
		$quoteInstaller->addAttribute('quote', 'company_address', ['type' => 'text']);
		$quoteInstaller->addAttribute('quote', 'customer_note', ['type' => 'text']);
		$quoteInstaller->addAttribute('quote', 'delivery_date', ['type' => 'text']);
		$quoteInstaller->addAttribute('quote_item', 'customer_note', ['type' => 'text']);

		$customerSetup->addAttribute(Customer::ENTITY, 'default_vat_invoice', [
			'label' => 'Default VAT Invoice',
			'type' => 'text',
			'input' => 'text',
			'visible' => true,
			'required' => false,
			'system' => false,
			'user_defined' => false,
			'position' => 640,
			'visible_on_front' => true,
		]);
		$vatInvoiceAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'default_vat_invoice');
		$vatInvoiceAttribute->addData([
			'attribute_set_id' => $attributeSetId,
			'attribute_group_id' => $attributeGroupId
		]);
		$vatInvoiceAttribute->save();

		$installer->endSetup();
	}
}
