<?php


namespace Magenest\Affiliate\Block\Adminhtml\Account;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Magenest\Affiliate\Model\Account;

/**
 * Class Edit
 * @package Magenest\Affiliate\Block\Adminhtml\Account
 */
class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Edit constructor.
     *
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Registry $coreRegistry,
        Context $context,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * Initialize Account edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'account_id';
        $this->_blockGroup = 'Magenest_Affiliate';
        $this->_controller = 'adminhtml_account';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Account'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
    }

    /**
     * Retrieve text for header element depending on loaded Account
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Account $account */
        $account = $this->_coreRegistry->registry('magenest_affiliate_account');
        if ($account->getId()) {
            return __("Edit Account '%1'", $this->escapeHtml($account->getCustomer_id()));
        }

        return __('New Account');
    }
}
