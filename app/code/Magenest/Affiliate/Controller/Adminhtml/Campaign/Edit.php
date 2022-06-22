<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Campaign;

use Magenest\Affiliate\Helper\Data;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Campaign;

/**
 * Class Edit
 * @package Magenest\Affiliate\Controller\Adminhtml\Campaign
 */
class Edit extends Campaign
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|ResponseInterface|ResultInterface|Page
     */
    public function execute()
    {
        /** @var \Magenest\Affiliate\Model\Campaign $campaign */
        $campaign = $this->_initCampaign();
        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::campaign');
        $resultPage->getConfig()->getTitle()->set(__('Campaigns'));

        $title = $campaign->getId() ? __('Edit Campaign "%1"', $campaign->getName()) : __('New Campaign');
        $resultPage->getConfig()->getTitle()->prepend($title);

        $data = $this->_getSession()->getData('affiliate_campaign_data', true);
        if (!empty($data)) {
            $campaign->setData($data);
        }
        $this->_coreRegistry->register('current_campaign', $campaign);

        $model = $this->_objectManager->create('Magento\SalesRule\Model\Rule');
        if (!empty($campaign->getData())) {
            $model->addData($campaign->getData());
        }

        // format discount amount
        $model->setDiscountAmount(Data::niceNumberFormat($model->getDiscountAmount()));

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $model->getActions()->setJsFormObject('rule_actions_fieldset');

        $this->_coreRegistry->register('current_campaign_rule', $model);

        return $resultPage;
    }
}
