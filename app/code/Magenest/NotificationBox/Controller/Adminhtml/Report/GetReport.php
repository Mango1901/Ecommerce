<?php

namespace Magenest\NotificationBox\Controller\Adminhtml\Report;

use DateInterval;
use DatePeriod;
use DateTime;
use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\Collection;
use Magento\Framework\Controller\Result\JsonFactory;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;

/**
 * Class GetReport
 * @package Magenest\InstagramShop\Controller\Adminhtml\Report
 */
class GetReport extends \Magento\Backend\App\Action
{
    /** @var JsonFactory */
    protected $resultJsonFactory;

    /** @var Json */
    protected $serialize;


    /** @var Helper */
    protected $helper;

    /** @var Collection */
    protected $collection;

    /** @var CustomerTokenFactory  */
    protected $customerTokenFactory;

    /** @var CustomerToken  */
    protected $customerToken;

    /**
     * @param Action\Context $context
     * @param Json $serialize
     * @param Helper $helper
     * @param Collection $collection
     * @param JsonFactory $resultJsonFactory
     * @param CustomerToken $customerToken
     * @param CustomerTokenFactory $customerTokenFactory
     */
    public function __construct(
        Action\Context $context,
        Json $serialize,
        Helper $helper,
        Collection $collection,
        JsonFactory $resultJsonFactory,
        CustomerToken $customerToken,
        CustomerTokenFactory $customerTokenFactory
    )
    {
        parent::__construct($context);
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->serialize = $serialize;
        $this->helper = $helper;
        $this->collection = $collection;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerToken = $customerToken;
        $this->customerTokenFactory = $customerTokenFactory;
    }

    /**
     * Save Report
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $dataReport =[];
        $data =[];
        $listDayHaveToken =[];
        $from = $this->getRequest()->getParam('from');
        //$from     .= ' 00:00:00';
        $to = $this->getRequest()->getParam('to');

        $begin = new DateTime($from);
        $end = new DateTime($to.' 23:59:59');

        $interval = DateInterval::createFromDateString('1 day');
        $period = new DatePeriod($begin, $interval, $end);
        $connection = $this->collection->getConnection();
        $allToken = $this->collection->addFieldToFilter('created_at', array('gteq' => $from))
            ->addFieldToFilter('created_at', array('lteq' => $to))
            ->getSelect()
            ->columns('COUNT(created_at) as total')
            ->group('created_at');
        $allToken = $connection->fetchAll($allToken);
        foreach ($allToken as $token){
            $listDayHaveToken[]=$token['created_at'];
            $dataReport[] = ['day' => $token['created_at'], 'total' =>(int)$token['total']];
        }
        foreach ($period as $dt) {
            $day = $dt->format('Y-m-d');
            if(!in_array($day,$listDayHaveToken)){
                $data []= [ 'day'=>$day,'total'=> 0];
            }
            else{
                $index = array_search($day,$listDayHaveToken);
                $data []= [ 'day'=>$day,'total'=> $dataReport[$index]['total']];
            }
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        return $resultJson->setData($data);
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_NotificationBox::report');
    }
}
