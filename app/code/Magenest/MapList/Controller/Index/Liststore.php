<?php

namespace Magenest\MapList\Controller\Index;

use Magenest\MapList\Controller\DefaultController;
use Magenest\MapList\Helper\Constant;

class Liststore extends DefaultController
{
    protected $_locationCollection;

    protected $resourceConnection;

    protected $_storeManagerInterface;

    protected $_locationFactory;

    protected $resultJsonFactory;


    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magenest\MapList\Model\LocationFactory $locationFactory,
        \Magenest\MapList\Model\ResourceModel\Location\CollectionFactory $locationCollection,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory

    )
    {
        $this->_locationCollection = $locationCollection;
        $this->resourceConnection = $resourceConnection;
        $this->_storeManagerInterface = $storeManagerInterface;
        $this->_locationFactory = $locationFactory;
        $this->resultJsonFactory = $resultJsonFactory;

        parent::__construct($context, $resultPageFactory, $resultForwardFactory, $registry, $loggerInterface, $locationFactory);
    }

    public function execute()
    {
        $center = $this->getRequest()->getParam('center');
        $rad = $this->getRequest()->getParam('rad');
        $filter = $this->getRequest()->getParam('filter') ? : [];
        $unit1 = $this->getRequest()->getParam('unit');
        $unit = ($unit1 == 'google.maps.UnitSystem.METRIC') ? 6371 : 3959;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get(\Magento\Framework\App\ResourceConnection::class);
        $connection = $this->resourceConnection->getConnection();
        $tableName = $resource->getTableName(Constant::LOCATION_TABLE);
        $result = array();
        $listStore = array();


        //get current store view
        $currentStore = $this->_storeManagerInterface->getStore();
        $currentStoreId = $currentStore->getId();

        if ($center == '') {
            $check = false;
            foreach ($filter as $value) {
                if ($value != null) {
                    $check = true;
                    break;
                }
            }
            if ($check == true) {
                $sql = $connection->select()
                    ->from(
                        array('l' => $tableName),
                        array('*')
                    )
                    ->where('is_active = ?', 1)
                    ->where(new \Zend_Db_Expr("(store like '%0%' OR store like '%" . $currentStoreId . "%')"));
                  $filterOption = [
                      0 => $sql->where('parking = ?', $filter[0]),
                      1 => $sql->where('atm = ?', $filter[1]),
                      2 => $sql->where("payment_methods like '%" . $filter[2] . "%'"),
                      3 => $sql->where("brands like '%\"" . $filter[3] . "\"%'")
                  ];
                  foreach($filterOption as $index => $value){
                      if($filter[$index] != ''){
                          $sql = $value;
                      }
                  }
                $result = $connection->fetchAll($sql);
            } else {
                //select all store
                $LocationModel = $this->_locationFactory->create();
                $LocationData = $LocationModel->getCollection()
                    ->addFieldToFilter('is_active', '1')
                    ->getData();
                foreach ($LocationData as $key => $locationValue) {
                    $posStore = strpos($locationValue['store'], $currentStoreId);
                    $allStoreView = strpos($locationValue['store'], "0");
                    if ($posStore !== false || $allStoreView !== false) {
                        $listStore[] = $locationValue;
                    }
                }
                $result = $listStore;
            }
        } elseif ($center != '' && $rad == 0) {
            //search nearest store
            $cosCenterLat = cos(deg2rad($center['lat']));
            $centerLng = deg2rad($center['lng']);
            $sinCenterLat = sin(deg2rad($center['lat']));
            $sql = $connection->select()
                ->from(
                    array('l' => $tableName),
                    array('location_id',
                        'title',
                        'small_image',
                        'address',
                        'opening_hours',
                        'latitude',
                        'longitude',
                        'distance' => new \Zend_Db_Expr(
                            "{$unit}* acos({$cosCenterLat} * cos(radians(latitude)) * cos(radians(longitude) -
                                        {$centerLng}) +{$sinCenterLat} * sin(radians(latitude )))"
                        )
                    )
                )
                ->where('is_active = ?', 1)
                ->where(new \Zend_Db_Expr("(store like '%0%' OR store like '%" . $currentStoreId . "%')"))
                ->having('distance')
                ->order('distance');
            $filterOption = [
                0 => $sql->where('parking = ?', $filter[0]),
                1 => $sql->where('atm = ?', $filter[1]),
                2 => $sql->where("payment_methods like '%" . $filter[2] . "%'"),
                3 => $sql->where("brands like '%\"" . $filter[3] . "\"%'")
            ];
            foreach($filterOption as $index => $value){
                if($filter[$index] != ''){
                    $sql = $value;
                }
            }
            $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.
        } elseif ($center != '' && $rad != 0) {
            //search store by distance and order by distance
            $cosCenterLat = cos(deg2rad($center['lat']));
            $centerLng = deg2rad($center['lng']);
            $sinCenterLat = sin(deg2rad($center['lat']));
            $sql = $connection->select()
                ->from(
                    array('l' => $tableName),
                    array('location_id',
                        'title',
                        'address',
                        'small_image',
                        'opening_hours',
                        'latitude',
                        'longitude',
                        'distance' => new \Zend_Db_Expr(
                            "{$unit}* acos({$cosCenterLat} * cos(radians(latitude)) * cos(radians(longitude) -
                                        {$centerLng}) +{$sinCenterLat} * sin(radians(latitude )))"
                        )
                    )
                )
                ->where('is_active = ?', 1)
                ->where(new \Zend_Db_Expr("(store like '%0%' OR store like '%" . $currentStoreId . "%')"))
                ->having('distance <' . $rad)
                ->order('distance');
            $filterOption = [
                0 => $sql->where('parking = ?', $filter[0]),
                1 => $sql->where('atm = ?', $filter[1]),
                2 => $sql->where("payment_methods like '%" . $filter[2] . "%'"),
                3 => $sql->where("brands like '%\"" . $filter[3] . "\"%'")
            ];
            foreach($filterOption as $index => $value){
                if($filter[$index] != ''){
                    $sql = $value;
                }
            }
            $result = $connection->fetchAll($sql); // gives associated array, table fields as key in array.
        }
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData($result);
    }
}
