<?php

namespace Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magenest\AffiliateOpt\Helper\Reports as ReportsHelper;
use Zend_Db_Expr;

/**
 * Class AffiliateOpt
 * @package Magenest\AffiliateOpt\Block\Adminhtml\Reports\Dashboard
 */
class Transaction extends Reports
{
    /**
     * @return bool|string
     * @throws NoSuchEntityException
     */
    public function mpAffiliateChartData()
    {
        $date = $this->getReportsHelper()->getDateRange();
        $data = $this->getRewardData($date[0], $date[1]);
        $compareData = false;
        if ($this->getReportsHelper()->isCompare()) {
            $compareData = $this->getRewardData($date[2], $date[3]);
        }

        if ($data || $compareData) {
            return ReportsHelper::jsonEncode(
                [
                    'labelColor' => $this->getLabelColor(),
                    'data' => $data,
                    'compareData' => $compareData,
                    'isCompare' => $this->getReportsHelper()->isCompare(),
                    'priceFormat' => $this->reportsHelper->getPriceFormat(),
                    'maintainAspectRatio' => false
                ]
            );
        }

        return false;
    }

    /**
     * @return array
     */
    public function getLabelColor()
    {
        return $this->reportsHelper->getLabelColorsStatus();
    }

    /**
     * @param $from
     * @param $to
     *
     * @return array
     */
    public function getRewardData($from, $to)
    {
        $transactionCollection = $this->collection->create()->addFieldToFilter('created_at', ['gteq' => $from])
            ->addFieldToSelect([])
            ->addFieldToFilter('created_at', ['lteq' => $to]);

        $storeId = $this->_request->getParam('store', 0);
        if ($storeId) {
            $transactionCollection->addFieldToFilter('store_id', $storeId);
        }

        $field = ['holding', 'complete', 'cancel'];
        foreach ($this->getMpTransactionKeys() as $key => $value) {
            $transactionCollection->getSelect()->columns(
                [
                    $field[$key] => new Zend_Db_Expr(
                        sprintf(
                            'SUM(CASE WHEN main_table.status = \'%s\' THEN main_table.amount ELSE 0 END )',
                            $value
                        )
                    )
                ]
            );
        }

        $rewardData = false;
        if ($transactionCollection->getSize() > 0) {
            $rewardData = $transactionCollection->getFirstItem()->getData();
            unset($rewardData['transaction_id']);
            $rewardData = array_values($rewardData);
            foreach ($rewardData as $key => $value) {
                $rewardData[$key] = floatval($value);
            }
        }

        return $rewardData;
    }

    /**
     * @return array
     */
    public function getMpTransactionKeys()
    {
        return $this->reportsHelper->getTransactionStatusKeys();
    }

    /**
     * @return Phrase|string
     */
    public function getTitle()
    {
        return __('Affiliate Transaction');
    }
}
