<?php

namespace Magenest\PhotoReview\Block\Adminhtml\Edit;

use Magenest\PhotoReview\Block\Adminhtml\Review\Photo;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Review\Block\Adminhtml\Rating\Detailed;
use Magento\Review\Block\Adminhtml\Rating\Summary;
use Magento\Review\Helper\Data;
use Magento\Store\Model\Store;

class Form extends \Magento\Review\Block\Adminhtml\Edit\Form
{
    /**
     * @var \Magenest\PhotoReview\Helper\Data
     */
    protected $_photoReviewHelper;

    public function __construct(
        \Magenest\PhotoReview\Helper\Data $photoReviewHelper,
        Context                           $context,
        Registry                          $registry,
        FormFactory                       $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        CustomerRepositoryInterface       $customerRepository,
        ProductFactory                    $productFactory,
        Data                              $reviewData,
        array                             $data = []
    ) {
        $this->_photoReviewHelper = $photoReviewHelper;
        parent::__construct($context, $registry, $formFactory, $systemStore, $customerRepository, $productFactory, $reviewData, $data);
    }

    /**
     * @return \Magento\Review\Block\Adminhtml\Edit\Form
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function _prepareForm()
    {
        $review = $this->_coreRegistry->registry('review_data');
        $product = $this->_productFactory->create()->load($review->getEntityPkValue());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form',
                    'action' => $this->getUrl(
                        'review/*/save',
                        [
                            'id' => $this->getRequest()->getParam('id'),
                            'ret' => $this->_coreRegistry->registry('ret')
                        ]
                    ),
                    'method' => 'post',
                ],
            ]
        );

        $fieldset = $form->addFieldset(
            'review_details',
            ['legend' => __('Review Details'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'product_name',
            'note',
            [
                'label' => __('Product'),
                'text' => '<a href="' . $this->getUrl(
                        'catalog/product/edit',
                        ['id' => $product->getId()]
                    ) . '" onclick="this.target=\'blank\'">' . $this->escapeHtml(
                        $product->getName()
                    ) . '</a>'
            ]
        );

        try {
            $customer = $this->customerRepository->getById($review->getCustomerId());
            $customerText = __(
                '<a href="%1" onclick="this.target=\'blank\'">%2 %3</a> <a href="mailto:%4">(%4)</a>',
                $this->getUrl('customer/index/edit', ['id' => $customer->getId(), 'active_tab' => 'review']),
                $this->escapeHtml($customer->getFirstname()),
                $this->escapeHtml($customer->getLastname()),
                $this->escapeHtml($customer->getEmail())
            );
        } catch (NoSuchEntityException $e) {
            $customerText = ($review->getStoreId() == Store::DEFAULT_STORE_ID)
                ? __('Administrator') : __('Guest');
        }

        $fieldset->addField('customer', 'note', ['label' => __('Author'), 'text' => $customerText]);

        $fieldset->addField(
            'summary-rating',
            'note',
            [
                'label' => __('Summary Rating'),
                'text' => $this->getLayout()->createBlock(
                    Summary::class
                )->toHtml()
            ]
        );

        $fieldset->addField(
            'detailed-rating',
            'note',
            [
                'label' => __('Detailed Rating'),
                'required' => true,
                'text' => '<div id="rating_detail">' . $this->getLayout()->createBlock(
                        Detailed::class
                    )->toHtml() . '</div>'
            ]
        );

        $fieldset->addField(
            'status_id',
            'select',
            [
                'label' => __('Status'),
                'required' => true,
                'name' => 'status_id',
                'values' => $this->_reviewData->getReviewStatusesOptionArray()
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->hasSingleStore()) {
            $field = $fieldset->addField(
                'select_stores',
                'multiselect',
                [
                    'label' => __('Visibility'),
                    'required' => true,
                    'name' => 'stores[]',
                    'values' => $this->_systemStore->getStoreValuesForForm()
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                Element::class
            );
            $field->setRenderer($renderer);
            $review->setSelectStores($review->getStores());
        } else {
            $fieldset->addField(
                'select_stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $review->setSelectStores($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'nickname',
            'text',
            ['label' => __('Nickname'), 'required' => true, 'name' => 'nickname']
        );

        $fieldset->addField(
            'title',
            'multiselect',
            [
                'label' => __('Summary of Review'),
                'required' => false,
                'name' => 'title[]',
                'values' => $this->_photoReviewHelper->getLabelSummary()
            ]
        );

        $fieldset->addField(
            'detail',
            'textarea',
            ['label' => __('Review'), 'required' => true, 'name' => 'detail', 'style' => 'height:24em;']
        );
        $data = $review->getData();
        if ($review->getReviewId()) {
            $data = array_merge($data, $this->_photoReviewHelper->getCustomReviewDetail($data['review_id'], $data['detail_id']));
        }

        $fieldset->addField(
            'video_review',
            'note',
            [
                'label' => __('Video Review'),
                'text' => $this->getLayout()->createBlock(
                    \Magenest\PhotoReview\Block\Adminhtml\Review\Video::class
                )->toHtml()
            ]
        );


        $fieldset->addField(
            'photo_review',
            'note',
            [
                'label' => __('Photo Review'),
                'text' => $this->getLayout()->createBlock(
                    Photo::class
                )->toHtml()
            ]
        );

        $form->setUseContainer(true);
        $form->setValues($data);
        $this->setForm($form);
    }
}
