<?php
namespace Magenest\Affiliate\Block\Adminhtml\Account\Edit\Tab;

use Magenest\Affiliate\Block\Adminhtml\Account\Edit\Tab\Element\Image;
use Magenest\Affiliate\Block\Adminhtml\Customer\Grid;
use Magenest\Affiliate\Model\Account\BankInfo;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Config\Model\Config\Source\Yesno;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Registry;
use Magenest\Affiliate\Helper\Data;
use Magenest\Affiliate\Model\Account\Group;
use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\AccountFactory;
use Magento\Customer\Ui\Component\Listing\Column\Group\Options;
use Magenest\Directory\Model\ResourceModel\City\Collection as City;
use Magenest\Directory\Model\ResourceModel\District\Collection as District;
use Magenest\Directory\Model\ResourceModel\Ward\Collection as Ward;
use Magento\Directory\Model\Config\Source\Country;


class Account extends Generic implements TabInterface
{
    /**
     * @var Yesno
     */
    protected $_boolean;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var Group
     */
    protected $_group;

    /**
     * @var AccountFactory
     */
    protected $_accountFactory;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    protected $bankList;

    /**
     * @var Options
     */
    protected $customerGroup;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var City
     */
    protected $city;

    /**
     * @var District
     */
    protected $district;

    /**
     * @var Ward
     */
    protected $ward;


    /**
     * @param Yesno $boolean
     * @param Status $status
     * @param Group $group
     * @param Context $context
     * @param Registry $registry
     * @param CustomerFactory $customerFactory
     * @param AccountFactory $accountFactory
     * @param FormFactory $formFactory
     * @param Data $helperData
     * @param PriceCurrencyInterface $priceCurrency
     * @param BankInfo $bankInfo
     * @param Options $customerGroup
     * @param Country $country
     * @param City $city
     * @param District $district
     * @param Ward $ward
     * @param array $data
     */
    public function __construct(
        Yesno $boolean,
        Status $status,
        Group $group,
        Context $context,
        Registry $registry,
        CustomerFactory $customerFactory,
        AccountFactory $accountFactory,
        FormFactory $formFactory,
        Data $helperData,
        PriceCurrencyInterface $priceCurrency,
        BankInfo $bankInfo,
        Options $customerGroup,
        Country $country,
        City $city,
        District $district,
        Ward $ward,
        array $data = []
    ) {
        $this->_accountFactory  = $accountFactory;
        $this->_customerFactory = $customerFactory;
        $this->_boolean         = $boolean;
        $this->_status          = $status;
        $this->_group           = $group;
        $this->helperData       = $helperData;
        $this->priceCurrency    = $priceCurrency;
        $this->bankList         = $bankInfo;
        $this->customerGroup    = $customerGroup;
        $this->country = $country;
        $this->city = $city;
        $this->district = $district;
        $this->ward = $ward;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _prepareForm()
    {
        /** @var \Magenest\Affiliate\Model\Account $account */
        $account = $this->_coreRegistry->registry('current_account');
        $form    = $this->_formFactory->create();
        $form->setHtmlIdPrefix('account_');
        $form->setFieldNameSuffix('account');

        if ($account->getId()) {
            $this->editAccount($form, $account);
        } else {
            $this->createNewAccount($form);
        }
        $form->addValues($account->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @param $form
     * @param $account
     *
     * @throws LocalizedException
     */
    public function editAccount($form, $account)
    {
        $fieldset = $form->addFieldset('base_fieldset', [
            'legend' => __('Account Information'),
            'class'  => 'fieldset-wide'
        ]);
        $fieldset->addType('image_big', Image::class);

        $fieldset->addField('customer_id', 'hidden', ['name' => 'customer_id']);
        $customer         = $this->_customerFactory->create()->load($account->getCustomerId());
        $baseCurrencyCode = $customer->getStore()->getBaseCurrencyCode();
        $fieldset->addField('customer_name', 'link', [
            'href'   => $this->getUrl('customer/index/edit', ['id' => $customer->getId()]),
            'name'   => 'customer_name',
            'label'  => __('Customer'),
            'title'  => __('Customer'),
            'value'  => $customer->getName() . ' <' . $this->escapeHtml($customer->getEmail()) . '>',
            'target' => '_blank',
            'class'  => 'control-value',
            'style'  => 'text-decoration: none'
        ]);

        $fieldset->addField('group_id', 'select', [
            'name'   => 'group_id',
            'label'  => __('Affiliate Group'),
            'title'  => __('Affiliate Group'),
            'values' => $this->_group->toOptionArray()
        ]);

        $fieldset->addField('customer_group_id', 'select', [
            'name'     => 'customer_group_id',
            'value'    => $customer->getGroupId(),
            'label'    => __('Customer Group'),
            'title'    => __('Customer Group'),
            'values'   => $this->customerGroup->toOptionArray()
        ]);

        $fieldset->addField('balance', 'note', [
            'label' => __('Balance'),
            'text'  => $this->priceCurrency->format(
                $account->getBalance(),
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                null,
                $baseCurrencyCode
            )
        ]);

        $fieldset->addField('holding_balance', 'note', [
            'label' => __('Holding Balance'),
            'text'  => $this->priceCurrency->format(
                $account->getHoldingBalance(),
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                null,
                $baseCurrencyCode
            )
        ]);

        if ($account->getParent()) {
            $fieldset->addField('parent', 'hidden', ['name' => 'parent']);
            $parentAccount = $this->_accountFactory->create()->load($account->getParent());
            if ($parentAccount->getId()) {
                $parentCustomer = $this->_customerFactory->create()->load($parentAccount->getCustomerId());
                $fieldset->addField('parent_account', 'link', [
                    'href'   => $this->getUrl('affiliate/account/edit', ['id' => $account->getParent()]),
                    'name'   => 'parent_account',
                    'label'  => __('Referred By'),
                    'title'  => __('Referred By'),
                    'value'  => $parentCustomer->getName() . ' <' .
                        $this->escapeHtml($parentCustomer->getEmail())
                        . '>',
                    'target' => '_blank',
                    'class'  => 'control-value',
                    'style'  => 'text-decoration: none'
                ]);
            }
        }

        $fieldset->addField('code', 'note', [
            'label' => __('Referral Code'),
            'text'  => $account->getCode(),
        ]);

        $fieldset->addField('attention', 'textarea', [
            'name'     => 'attention',
            'label'    => __('Attention'),
            'title'    => __('Attention'),
        ]);

        $fieldset->addField('telephone', 'text', [
            'name'   => 'telephone',
            'label'  => __('Phone Number'),
            'title'  => __('Phone Number'),
            'required' => true,
            'class' => 'required-entry validate-telephone-require'
        ]);
        $fieldset->addField('employee_id', 'text', [
            'name'   => 'employee_id',
            'label'  => __('Kangaroo Employee ID'),
            'title'  => __('Kangaroo Employee ID')
        ]);
        $fieldset->addField('id_number', 'text', [
            'name'   => 'id_number',
            'label'  => __('ID Number'),
            'title'  => __('ID Number'),
            'required' => true,
            'class' => 'required-entry integer validate-greater-than-zero validate-id-number'
        ]);
        $fieldset->addField('license_date', 'date', [
            'name' => 'license_date',
            'label' => __('License Date'),
            'title' => __('License Date'),
            'date_format' => 'yyyy-MM-dd',
            'time_format' => false,
            'required' => true,
        ]);
        $fieldset->addField('issued_by', 'text', [
            'name'   => 'issued_by',
            'label'  => __('Issued By'),
            'title'  => __('Issued By'),
            'required' => true,
        ]);
        $fieldset->addField('city_id', 'select', [
            'name'   => 'city_id',
            'values'   => $this->city->toOptionArrayCustom(),
            'label'  => __('City'),
            'title'  => __('City'),
            'required' => true,
        ]);

         $fieldset->addField('district_id', 'select', [
            'name'   => 'district_id',
            'values'   => $this->district->toOptionArrayCustom($account->getData('city_id')),
            'label'  => __('District'),
            'title'  => __('District'),
            'required' => true,
        ]);
        $fieldset->addField('ward_id', 'select', [
            'name'   => 'ward_id',
            'values'   => $this->ward->toOptionArrayCustom($account->getData('district_id')),
            'label'  => __('Ward'),
            'title'  => __('Ward'),
            'required' => true,
        ]);
        $fieldset->addField('id_front', 'image_big', [
            'name' => 'id_front',
            'label' => __('ID Front'),
            'title' => __('ID Front'),
            'note' => 'Allow image type: jpg, jpeg, png'
        ]);
        $fieldset->addField('id_back', 'image_big', [
            'name' => 'id_back',
            'label' => __('ID Back'),
            'title' => __('ID Back'),
            'note' => 'Allow image type: jpg, jpeg, png'
        ]);

        $fieldset->addField('email_notification', 'select', [
            'name'   => 'email_notification',
            'label'  => __('Email Notification'),
            'title'  => __('Email Notification'),
            'values' => $this->_boolean->toOptionArray(),
        ]);
        $fieldset->addField('bank_no', 'select', [
            'name'   => 'bank_no',
            'label'  => __('Bank'),
            'title'  => __('Bank'),
            'values' => $this->bankList->toOptionArray(),
            'note' => __("With BACABANK and COOPBANK, please enter card number instead of bank number!")
        ]);

        $title = $account->getBankType() ? __('Card Number') : __('Bank Number');

        $fieldset->addField('acc_no', 'text', [
            'name'   => 'acc_no',
            'label'  => $title,
            'title'  => $title,
            'class' => 'validate-digits',
        ]);
        $fieldset->addField('account_name', 'text', [
            'name'   => 'account_name',
            'label'  => __('Bank Owner'),
            'title'  => __('Bank Owner'),
            'class' => 'validate-alpha-with-spaces',
        ]);

        $fieldset->addField('status', 'select', [
            'name'     => 'status',
            'label'    => __('Status'),
            'title'    => __('Status'),
            'required' => true,
            'values'   => $this->_status->toOptionArray(),
        ]);

        $fieldset->addField('note', 'textarea', [
            'name'     => 'note',
            'label'    => __('Note'),
            'title'    => __('Note'),
        ]);
    }

    /**
     * @param $form
     */
    public function createNewAccount($form)
    {
        $fieldset = $form->addFieldset('base_fieldset1', [
            'legend' => __('Account Information'),
            'class'  => 'fieldset-wide'
        ]);
        $fieldset->addType('image_big', Image::class);

        $fieldset->addField('customer_id', 'hidden', [
            'name' => 'customer_id'
        ]);

        $this->helperData->addCustomerEmailFieldset($fieldset, 'account', $this->getAjaxUrl(), Grid::CREATE_ACCOUNT_ACTION);

        $fieldset->addField('group_id', 'select', [
            'name'     => 'group',
            'label'    => __('Affiliate Group'),
            'title'    => __('Affiliate Group'),
            'required' => true,
            'values'   => $this->_group->toOptionArray()
        ]);

        $fieldset->addField('customer_group_id', 'select', [
            'name'     => 'customer_group_id',
            'label'    => __('Customer Group'),
            'title'    => __('Customer Group'),
            'required' => true,
            'values'   => $this->customerGroup->toOptionArray()
        ]);

        $fieldset->addField('parent', 'text', [
            'name'  => 'parent',
            'label' => __('Referred By'),
            'title' => __('Referred By'),
            'class' => 'validate-number',
            'note'  => __('Affiliate account Id')
        ]);

        $fieldset->addField('attention', 'textarea', [
            'name'     => 'attention',
            'label'    => __('Attention'),
            'title'    => __('Attention'),
        ]);

        $fieldset->addField('email_notification', 'select', [
            'name'   => 'email_notification',
            'label'  => __('Email Notification'),
            'title'  => __('Email Notification'),
            'values' => $this->_boolean->toOptionArray(),
        ]);

        $fieldset->addField('telephone', 'text', [
            'name'   => 'telephone',
            'label'  => __('Phone Number'),
            'title'  => __('Phone Number'),
            'required' => true,
            'class' => 'required-entry validate-telephone-require'
        ]);
        $fieldset->addField('employee_id', 'text', [
            'name'   => 'employee_id',
            'label'  => __('Kangaroo Employee ID'),
            'title'  => __('Kangaroo Employee ID')
        ]);
        $fieldset->addField('id_number', 'text', [
            'name'   => 'id_number',
            'label'  => __('ID Number'),
            'title'  => __('ID Number'),
            'required' => true,
            'class' => 'required-entry integer validate-greater-than-zero validate-id-number'
        ]);
        $fieldset->addField('license_date', 'date', [
            'name' => 'license_date',
            'label' => __('License Date'),
            'title' => __('License Date'),
            'date_format' => 'yyyy-MM-dd',
            'time_format' => false,
            'required' => true,
        ]);
        $fieldset->addField('issued_by', 'text', [
            'name'   => 'issued_by',
            'label'  => __('Issued By'),
            'title'  => __('Issued By'),
            'required' => true,
        ]);
        $fieldset->addField('country_id', 'hidden', [
            'name'   => 'country_id',
            'value'   => 'VN',
            'visible' => false ,
            'label'  => __('Country'),
            'title'  => __('Country'),
            'required' => true,
        ]);
        $fieldset->addField('city_id', 'select', [
            'name'   => 'city_id',
            'values'   => $this->city->toOptionArrayCustom(),
            'label'  => __('City'),
            'title'  => __('City'),
            'required' => true,
        ]);
        $fieldset->addField('district_id', 'select', [
            'name'   => 'district_id',
            'values'   => $this->district->toOptionArrayCustom(),
            'label'  => __('District'),
            'title'  => __('District'),
            'required' => true,
        ]);
        $fieldset->addField('ward_id', 'select', [
            'name'   => 'ward_id',
            'values'   => $this->ward->toOptionArrayCustom(),
            'label'  => __('Ward'),
            'title'  => __('Ward'),
            'required' => true,
        ]);
        $fieldset->addField('id_front', 'image_big', [
            'name' => 'id_front',
            'label' => __('ID Front'),
            'title' => __('ID Front'),
            'required' => true,
            'note' => 'Allow image type: jpg, jpeg, png'
        ]);
        $fieldset->addField('id_back', 'image_big', [
            'name' => 'id_back',
            'label' => __('ID Back'),
            'title' => __('ID Back'),
            'required' => true,
            'note' => 'Allow image type: jpg, jpeg, png'
        ]);

        $fieldset->addField('bank_no', 'select', [
            'name'   => 'bank_no',
            'label'  => __('Bank'),
            'title'  => __('Bank'),
            'values' => $this->bankList->toOptionArray(),
            'note' => __("With BACABANK and COOPBANK, please enter card number instead of bank number!")
        ]);

        $title = __('Bank Number');

        $fieldset->addField('acc_no', 'text', [
            'name'   => 'acc_no',
            'label'  => $title,
            'title'  => $title,
            'class' => 'validate-digits',
        ]);
        $fieldset->addField('account_name', 'text', [
            'name'   => 'account_name',
            'label'  => __('Bank Owner'),
            'title'  => __('Bank Owner'),
            'class' => 'validate-alpha-with-spaces',
        ]);

        $fieldset->addField('status', 'select', [
            'name'     => 'status',
            'label'    => __('Status'),
            'title'    => __('Status'),
            'required' => true,
            'values'   => $this->_status->toOptionArray()
        ]);

        $fieldset->addField('note', 'textarea', [
            'name'     => 'note',
            'label'    => __('Note'),
            'title'    => __('Note'),
        ]);
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Account');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Get transaction grid url
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->getUrl('affiliate/customer/grid', ['action' => Grid::CREATE_ACCOUNT_ACTION]);
    }
}
