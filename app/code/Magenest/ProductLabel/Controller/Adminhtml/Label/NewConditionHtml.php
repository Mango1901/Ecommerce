<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_DynamicProductTabs extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_DynamicProductTabs
 */

namespace Magenest\ProductLabel\Controller\Adminhtml\Label;

use Magento\Backend\App\Action\Context;
use Magento\Rule\Model\Condition\AbstractCondition;

/**
 * Class NewConditionHtml
 * @package Magenest\ProductLabel\Controller\Adminhtml\Label
 */
class NewConditionHtml extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magenest\ProductLabel\Model\Rule\Condition\ProductFactory
     */
    protected $productFactory;

    /**
     * NewConditionHtml constructor.
     * @param \Magento\CatalogRule\Model\RuleFactory $ruleFactory
     * @param \Magenest\ProductLabel\Model\Rule\Condition\ProductFactory $productFactory
     * @param Context $context
     */
    public function __construct(
        \Magento\CatalogRule\Model\RuleFactory $ruleFactory,
        \Magenest\ProductLabel\Model\Rule\Condition\ProductFactory $productFactory,
        Context $context
    )
    {
        $this->ruleFactory = $ruleFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $formName = $this->getRequest()->getParam('form_namespace');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $model = $this->productFactory->create();
        $model->setId($id);
        $model->setRule($this->ruleFactory->create());
        $model->setType(reset($typeArr));
        $model->setPrefix('conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($formName);
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }
}
