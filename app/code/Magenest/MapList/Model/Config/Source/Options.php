<?php
/**
 * Created by PhpStorm.
 * User: katsu
 * Date: 19/09/2016
 * Time: 13:35
 */

namespace Magenest\MapList\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magenest\MapList\Model\ResourceModel\Brand\Collection;
use \Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Payment\Model\Config;


class Options implements ArrayInterface
{
    protected $_brandCollection;

    protected $_appConfigScopeConfigInterface;

    protected $_paymentModelConfig;

    protected $_paymentHelper;

    public function __construct(Collection $brandCollection, ScopeConfigInterface $appConfigScopeConfigInterface,
                                Config $paymentModelConfig, \Magento\Payment\Helper\Data $paymentHelper)
    {
        $this->_brandCollection = $brandCollection;
        $this->_appConfigScopeConfigInterface = $appConfigScopeConfigInterface;
        $this->_paymentModelConfig = $paymentModelConfig;
        $this->_paymentHelper = $paymentHelper;
    }

    public function getBrandOptions()
    {
        $collection = $this->_brandCollection->load()->getItems();
        $result = [];
        foreach ($collection as $value) {
            if($value->getStatus()==1){
                $result[] = [
                    'value' => $value->getBrandId(),
                    'label' => $value->getName()
                ];
            }
        }

        return $result;
    }

    public function getPaymentOptions()
    {
        $payments = $this->_paymentModelConfig->getActiveMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentModel) {
            $paymentTitle = $this->_appConfigScopeConfigInterface->getValue('payment/' . $paymentCode . '/title');
            $methods[$paymentCode] = array(
                'label' => $paymentTitle,
                'value' => $paymentCode
            );
        }
        return $methods;
    }

    public function getAllPaymentMethods()
    {
        $payments = $this->_paymentHelper->getPaymentMethods();
        $methods = array();
        foreach ($payments as $paymentCode => $paymentTitle) {
            if($paymentCode == 'cashondelivery' || $paymentCode == 'payflow_advanced')
            $methods[$paymentCode] = array(
                'label' => $paymentTitle['title'],
                'value' => $paymentCode
            );
        }
        return $methods;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return array('1' => __('Yes'), '0' => __('No'));
    }
}
