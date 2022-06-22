<?php

namespace Magenest\Customer\Setup\Patch\Data;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Indexer\Address\AttributeProvider;
use Magento\Customer\Setup\CustomerSetup;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Api\Data\AttributeSetInterfaceFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;

class AddFullNameAttribute implements DataPatchInterface
{
    const FULLNAME_CODE = 'fullname';

    /**
     * @var ModuleDataSetupInterface
     */
    private $setup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var AttributeSetInterfaceFactory
     */
    private $attributeSetFactory;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param EavSetupFactory $eavSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param AttributeSetInterfaceFactory $attributeSetFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        EavSetupFactory $eavSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        AttributeSetInterfaceFactory $attributeSetFactory
    ) {
        $this->setup = $setup;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $option = [
            'type' => 'static',
            'label' => 'Full Name',
            'input' => 'text',
            'sort_order' => 15,
            'validate_rules' => '{"max_text_length":255}',
            'position' => 15,
            'required' => false,
            'system' => 0,
            'user_defined' => true,
            'input_filter' => 'trim',
            'is_used_in_grid' => true,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => true
        ];

        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->setup]);
        $attributeSet = $this->attributeSetFactory->create();

        $customerEntity = $customerSetup->getEavConfig()->getEntityType(Customer::ENTITY);
        $attributeSetCustomerId = $customerEntity->getDefaultAttributeSetId();
        $attributeGroupCustomerId = $attributeSet->getDefaultGroupId($attributeSetCustomerId);

        $addressEntity = $customerSetup->getEavConfig()->getEntityType(AttributeProvider::ENTITY);
        $attributeSetAddressId = $addressEntity->getDefaultAttributeSetId();
        $attributeGroupAddressId = $attributeSet->getDefaultGroupId($attributeSetAddressId);

        $customerSetup->addAttribute(Customer::ENTITY, self::FULLNAME_CODE, $option);
        $customerSetup->addAttribute(AttributeProvider::ENTITY, self::FULLNAME_CODE, $option);

        $formCodes = [
            'adminhtml_customer',
            'adminhtml_checkout',
            'adminhtml_customer_address',
            'checkout_register',
            'customer_account_create',
            'customer_account_edit',
            'customer_address_edit',
            'customer_register_address'
        ];

        //add attribute to attribute set
        $attributeCustomer = $customerSetup->getEavConfig()->getAttribute(
            Customer::ENTITY,
            self::FULLNAME_CODE
        )->addData([
            'attribute_set_id' => $attributeSetCustomerId,
            'attribute_group_id' => $attributeGroupCustomerId,
            'used_in_forms' => $formCodes,
        ])->save();

        //add attribute to attribute set
        $attributeAddress = $customerSetup->getEavConfig()->getAttribute(
            AttributeProvider::ENTITY,
            self::FULLNAME_CODE
        )->addData([
            'attribute_set_id' => $attributeSetAddressId,
            'attribute_group_id' => $attributeGroupAddressId,
            'used_in_forms' => $formCodes,
        ])->save();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}
