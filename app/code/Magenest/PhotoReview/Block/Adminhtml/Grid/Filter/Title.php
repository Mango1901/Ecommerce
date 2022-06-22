<?php

namespace Magenest\PhotoReview\Block\Adminhtml\Grid\Filter;

use Magenest\PhotoReview\Block\Adminhtml\Form\Field\SummaryOptions;
use Magenest\PhotoReview\Helper\Data;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Filter\Select;
use Magento\Framework\DB\Helper;

/**
 * Title filter for grid
 */
class Title extends Select
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param Context $context
     * @param Helper $resourceHelper
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Helper $resourceHelper,
        Data $dataHelper,
        array $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * Get condition
     *
     * @return array
     */
    public function getCondition()
    {
        if ($valueCondition = $this->getValue()) {
            $likeExpression = $this->_resourceHelper->addLikeEscape($valueCondition, ['position' => 'any']);
            return ['like' => $likeExpression];
        }

        return ['like' => '%%'];
    }

    /**
     * Get grid options
     *
     * @return array
     */
    protected function _getOptions()
    {
        $result = [['label' => '', 'value' => '']];
        return array_merge($result, $this->dataHelper->getLabelSummary());
    }
}
