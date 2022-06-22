<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 24/09/2016
 * Time: 14:10
 */

namespace Magenest\MapList\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;

class Loadstate extends Action
{

    protected $resultPageFactory;
    protected $resultJsonFactory;
    protected $regionColFactory;

    public function __construct(
        Action\Context    $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Directory\Model\RegionFactory $regionColFactory
    ) {
        $this->regionColFactory     = $regionColFactory;
        $this->resultJsonFactory            = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $country_id = $this->getRequest()->getParam('country');
        $result   = $this->resultJsonFactory->create();
        $regions=$this->regionColFactory->create()->getCollection()
            ->addFieldToSelect(array('code', 'default_name'))
            ->addFieldToFilter('country_id', $country_id);

        return $result->setData($regions->getData());
    }
}
