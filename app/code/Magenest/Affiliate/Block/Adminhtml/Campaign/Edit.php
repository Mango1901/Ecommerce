<?php


namespace Magenest\Affiliate\Block\Adminhtml\Campaign;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magenest\Affiliate\Model\Campaign;

/**
 * Class Edit
 * @package Magenest\Affiliate\Block\Adminhtml\Campaign
 */
class Edit extends Container
{
    /**
     * Core registry
     *
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
     * Initialize Campaign edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_Affiliate';
        $this->_controller = 'adminhtml_campaign';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Campaign'));
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

        $this->buttonList->remove('delete');
    }

    /**
     * Retrieve text for header element depending on loaded Campaign
     *
     * @return Phrase|string
     */
    public function getHeaderText()
    {
        /** @var Campaign $campaign */
        $campaign = $this->_coreRegistry->registry('current_campaign');
        if ($campaign->getId()) {
            return __('Edit Campaign "%1"', $this->escapeHtml($campaign->getName()));
        }

        return __('New Campaign');
    }
}
