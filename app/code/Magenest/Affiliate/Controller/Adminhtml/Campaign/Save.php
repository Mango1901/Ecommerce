<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Campaign;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Controller\Adminhtml\Campaign;
use Magenest\Affiliate\Model\CampaignFactory;
use RuntimeException;
use Zend_Filter_Input;

/**
 * Class Save
 * @package Magenest\Affiliate\Controller\Adminhtml\Campaign
 */
class Save extends Campaign
{
    /**
     * @var Date
     */
    protected $_dateFilter;

    /**
     * Save constructor.
     *
     * @param Date $dateFilter
     * @param CampaignFactory $campaignFactory
     * @param Registry $registry
     * @param PageFactory $resultPageFactory
     * @param Context $context
     */
    public function __construct(
        Date $dateFilter,
        CampaignFactory $campaignFactory,
        Registry $registry,
        PageFactory $resultPageFactory,
        Context $context
    ) {
        $this->_dateFilter = $dateFilter;

        parent::__construct($context, $resultPageFactory, $registry, $campaignFactory);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPost('campaign')) {

            $data = $this->_filterData();

            $campaign = $this->_initCampaign();
            $campaign->loadPost($data);
            $this->_eventManager->dispatch('affiliate_campaign_prepare_save', [
                'campaign' => $campaign,
                'request' => $this->getRequest()
            ]);

            try {
                $campaign->save();
                $this->messageManager->addSuccessMessage(__('The Campaign has been saved successfully.'));
                $this->_getSession()->setData('affiliate_campaign_data', false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath('affiliate/*/edit', ['id' => $campaign->getId()]);

                    return $resultRedirect;
                }
                $resultRedirect->setPath('affiliate/*/');

                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving the Campaign.'));
            }
            $this->_getSession()->setData('affiliate_campaign_data', $data);
            $resultRedirect->setPath('affiliate/*/*', ['_current' => true]);

            return $resultRedirect;
        }
        $resultRedirect->setPath('affiliate/*/');

        return $resultRedirect;
    }

    /**
     * @return mixed
     */
    protected function _filterData()
    {
        $data = $this->getRequest()->getPostValue();
        if (isset($data['commission'])) {
            $data['commission'] = $this->correctTier($data['commission']);
        }

        $convertData = $data['campaign'];
        unset($data['campaign']);
        foreach ($convertData as $key => $value) {
            $data[$key] = $value;
        }

        /** Filter Date */
        $inputFilterDate = [];
        if (isset($data['from_date']) && $data['from_date']) {
            $inputFilterDate['from_date'] = $this->_dateFilter;
        }
        if (isset($data['to_date']) && $data['to_date']) {
            $inputFilterDate['to_date'] = $this->_dateFilter;
        }
        if (count($inputFilterDate)) {
            $inputFilter = new Zend_Filter_Input($inputFilterDate, [], $data);
            $data = $inputFilter->getUnescaped();
        }

        if (isset($data['website_ids'])) {
            if (is_array($data['website_ids'])) {
                $data['website_ids'] = implode(',', $data['website_ids']);
            }
        }
        if (isset($data['affiliate_group_ids'])) {
            if (is_array($data['affiliate_group_ids'])) {
                $data['affiliate_group_ids'] = implode(',', $data['affiliate_group_ids']);
            }
        }
        if (isset($data['discount_action'], $data['discount_amount']) && $data['discount_action'] === 'by_percent'
        ) {
            $data['discount_amount'] = min(100, $data['discount_amount']);
        }
        if (isset($data['rule']['conditions'])) {
            $data['conditions'] = $data['rule']['conditions'];
        }
        if (isset($data['rule']['actions'])) {
            $data['actions'] = $data['rule']['actions'];
        }
        unset($data['rule']);

        return $data;
    }

    /**
     * @param $data
     *
     * @return array
     */
    public function correctTier($data)
    {
        $correctData = [];
        $count = 1;
        foreach ($data as $item) {
            $item['name'] = __('Tier') . ' ' . $count;
            $correctData['tier_' . $count] = $item;
            if ($correctData['tier_' . $count]['value'] === null) {
                $correctData['tier_' . $count]['value'] = 0;
            }
            if ($correctData['tier_' . $count]['value_second'] === null) {
                $correctData['tier_' . $count]['value_second'] = 0;
            }
            $count++;
        }

        return $correctData;
    }
}
