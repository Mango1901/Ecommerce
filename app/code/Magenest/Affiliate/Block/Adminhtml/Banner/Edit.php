<?php


namespace Magenest\Affiliate\Block\Adminhtml\Banner;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;
use Magenest\Affiliate\Model\Banner;

/**
 * Class Edit
 * @package Magenest\Affiliate\Block\Adminhtml\Banner
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
     * Initialize Banner edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magenest_Affiliate';
        $this->_controller = 'adminhtml_banner';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Banner'));
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
     * Retrieve text for header element depending on loaded Banner
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Banner $banner */
        $banner = $this->_coreRegistry->registry('current_banner');
        if ($banner->getId()) {
            return __('Edit Banner "%1"', $this->escapeHtml($banner->getName()));
        }

        return __('New Banner');
    }
}
