<?php
namespace Magenest\SalesPerson\Block\Adminhtml\Order;

use Magento\Backend\Block\Template;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class WarningMessage extends Template
{
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $adminSession;

    /**
     * WarningMessage constructor.
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param Template\Context $context
     * @param array $data
     * @param JsonHelper|null $jsonHelper
     * @param DirectoryHelper|null $directoryHelper
     */
    public function __construct(
        \Magento\Backend\Model\Auth\Session $adminSession,
        Template\Context $context,
        array $data = [],
        ?JsonHelper $jsonHelper = null,
        ?DirectoryHelper $directoryHelper = null
    ) {
        $this->adminSession = $adminSession;
        parent::__construct($context, $data, $jsonHelper, $directoryHelper);
    }

    public function getWarningMessage()
    {
        return __("Please refresh your page before assign order");
    }

    public function getUserRole()
    {
        return $this->adminSession->getUser()->getRole()->getId();
    }
}
