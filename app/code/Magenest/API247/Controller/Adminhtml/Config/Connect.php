<?php
namespace Magenest\API247\Controller\Adminhtml\Config;

use Magenest\ViettelPost\Helper\Data;
use Magenest\ViettelPost\Model\District;
use Magenest\ViettelPost\Model\Province;
use Magenest\ViettelPost\Model\Wards;
use Magenest\API247\Model\API247Post;
use Magento\Backend\App\Action;

class Connect extends Action
{
    /** @var Data */
    private $_helperData;

    /** @var API247Post */
    private $api247;

    public function __construct(
        Data $helperData,
        API247Post $API247Post,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->_helperData = $helperData;
        $this->api247 = $API247Post;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        try {
            $this->api247->connect();
            $this->messageManager->addSuccessMessage(__("API247 API connect successfully!"));
        } catch (\Throwable $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $addressData = $this->api247->getCustomerClientHub();

        return $this->resultRedirectFactory->create()->setRefererOrBaseUrl();
    }
}
