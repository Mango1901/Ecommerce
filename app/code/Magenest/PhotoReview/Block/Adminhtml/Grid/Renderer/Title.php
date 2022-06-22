<?php

namespace Magenest\PhotoReview\Block\Adminhtml\Grid\Renderer;

use Magenest\PhotoReview\Helper\Data;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;

/**
 * Class Title
 *
 * Used for render title list in grid
 */
class Title extends AbstractRenderer
{
    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @param Context $context
     * @param Data $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data    $dataHelper,
        array   $data = []
    ) {
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    /**
     * Render review title
     *
     * @param DataObject $row
     * @return Phrase
     */
    public function render(DataObject $row)
    {
        $result = '';

        if ($title = $row->getTitle()) {
            $summaryArray = explode(',', $title);

            $isSummaryNotExist = false;

            if (is_array($summaryArray)) {
                foreach ($summaryArray as $value) {
                    foreach ($this->dataHelper->getLabelSummary() as $item) {
                        if ($item['value'] == $value) {
                            $isSummaryNotExist = true;
                            $result .= '<br>' . $item['label'];
                        }
                    }
                }
            }

            if (!$isSummaryNotExist) {
                return __($title);
            }
        }
        return __($result);
    }
}
