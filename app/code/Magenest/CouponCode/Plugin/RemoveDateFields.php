<?php
namespace Magenest\CouponCode\Plugin;

use Magenest\CouponCode\Model\Configurations\FromDate;
use Magenest\CouponCode\Model\Configurations\ToDate;

class RemoveDateFields
{
    /**
     * @var \Magenest\CouponCode\Helper\Data
     */
    protected $_dataHelper;

    /**
     * RemoveDateFields constructor.
     *
     * @param \Magenest\CouponCode\Helper\Data $dataHelper
     */
    public function __construct(\Magenest\CouponCode\Helper\Data $dataHelper)
    {
        $this->_dataHelper = $dataHelper;
    }

    /**
     * After get html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $subject
     * @param mixed $result
     * @return mixed
     */
    public function afterGetHtml(\Magento\Framework\Data\Form\Element\AbstractElement $subject, $result)
    {
        if ($this->_dataHelper->checkCommunityEdition()) {
            return $result;
        } elseif ((boolean) strpos($result, FromDate::CODE) !== true
            && (boolean) strpos($result, ToDate::CODE) !== true) {
            return $result;
        }
    }
}
