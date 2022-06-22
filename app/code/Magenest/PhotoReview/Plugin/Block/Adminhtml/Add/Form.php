<?php

namespace Magenest\PhotoReview\Plugin\Block\Adminhtml\Add;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Helper\SecureHtmlRenderer;
use Magento\Review\Block\Adminhtml\Add\Form as AddForm;
use Magento\Review\Helper\Data;
use Magento\Store\Model\System\Store;

class Form extends AddForm
{
    /**
     * @var \Magenest\PhotoReview\Helper\Data
     */
    private $_photoReviewHelper;

    public function __construct(
        Context                           $context,
        Registry                          $registry,
        FormFactory                       $formFactory,
        Store                             $systemStore,
        Data                              $reviewData,
        \Magenest\PhotoReview\Helper\Data $photoReviewHelper,
        array                             $data = [],
        ?SecureHtmlRenderer               $htmlRenderer = null
    ) {
        $this->_photoReviewHelper = $photoReviewHelper;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $systemStore,
            $reviewData,
            $data,
            $htmlRenderer
        );
    }

    /**
     * @param AddForm $object
     * @param DataForm $form
     * @return DataForm[]
     */
    public function beforeSetForm(AddForm $object, DataForm $form)
    {
        $fieldset = $form->getElement('add_review_form');
        $fieldset->removeField('title');
        $fieldset->addField(
            'title',
            'multiselect',
            [
                'title' => __('Summary of Review'),
                'label' => __('Summary of Review'),
                'required' => false,
                'name' => 'title[]',
                'values' => $this->_photoReviewHelper->getLabelSummary(),
            ]
        );

        return [$form];
    }
}
