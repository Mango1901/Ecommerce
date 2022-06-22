<?php

namespace Magenest\RewardPoints\Block\Adminhtml\Membership\Edit\Tab\Customer;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class CustomerWrapper
 *
 * @package Magenest\RewardPoints\Block\Adminhtml\Membership\Edit\Tab\Customer
 */
class CustomerWrapper extends Template
{
    protected $_template = 'tab/customer/grid.phtml';

    /**
     * @var Assign
     */
    protected $blockGrid;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Json
     */
    protected $jsonEncoder;

    /**
     * AssignProducts constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Json $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Json $jsonEncoder,
        array $data = []
    ) {
        $this->registry    = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * @return Assign|BlockInterface
     * @throws LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                Assign::class,
                'membership.product.grid'
            );
        }

        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     * @throws LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     * @throws LocalizedException
     */
    public function getCustomersJson()
    {
        $result      = [];
        $customerIds = $this->getBlockGrid()->getAssignedCustomer();
        if (is_array($customerIds)) {
            foreach ($customerIds as $id) {
                $result[$id] = $id;
            }
        }
        return $this->jsonEncoder->serialize($result);
    }
}
