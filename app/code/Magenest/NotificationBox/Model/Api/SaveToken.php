<?php
namespace Magenest\NotificationBox\Model\Api;

use Magenest\NotificationBox\Api\SaveTokenInterface;
use Magenest\NotificationBox\Helper\Helper;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\CustomerToken as CustomerTokenModel;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface as DateTime;
use Psr\Log\LoggerInterface;

class SaveToken implements SaveTokenInterface
{
    /** @var Helper  */
    protected $helper;

    /** @var CustomerTokenFactory  */
    protected $customerTokenFactory;

    /** @var CustomerToken  */
    protected $customerTokenResource;

    /** @var JsonFactory  */
    protected  $resultJsonFactory;

    /** @var  */
    protected $customerTokenCollection;

    /** @var StoreManagerInterface  */
    protected $storeManage;

    /** @var DateTime  */
    protected $dateTime;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var CustomerRepositoryInterface  */
    protected $customerRepositoryInterface;

    /**
     * @param Helper $helper
     * @param CustomerTokenFactory $customerTokenFactory
     * @param CustomerToken $customerToken
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $customerTokenCollection
     * @param StoreManagerInterface $storeManage
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     * @param CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        Helper $helper,
        CustomerTokenFactory $customerTokenFactory,
        CustomerToken  $customerToken,
        JsonFactory $resultJsonFactory,
        CollectionFactory $customerTokenCollection,
        StoreManagerInterface $storeManage,
        DateTime $dateTime,
        LoggerInterface $logger,
        CustomerRepositoryInterface $customerRepositoryInterface
    )
    {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->logger                   = $logger;
        $this->dateTime                 = $dateTime;
        $this->storeManage              = $storeManage;
        $this->customerTokenCollection  = $customerTokenCollection;
        $this->helper                   = $helper;
        $this->customerTokenFactory     = $customerTokenFactory;
        $this->customerTokenResource    = $customerToken;
        $this->resultJsonFactory        = $resultJsonFactory;
    }

    /** save customer Token
     * @param string $token
     * @param int $customerId
     * @param int $deviceId
     * @return array
     */
    public function registerForCustomer($token,$customerId,$deviceId){
        return $this->saveToken($token,$deviceId,$customerId);
    }

    /** save guest Token
     * @param string $token
     * @param int $deviceId
     * @return array
     */
    public function registerForGuest($token,$deviceId){
        return $this->saveToken($token,$deviceId);
    }


    /**
     * @param $token
     * @param $deviceId
     * @param null $customerId
     * @return array
     */
    public function saveToken($token,$deviceId,$customerId = null)
    {
        $result = [];
        //save token;
        try {
            $currentToken = $this->customerTokenFactory->create();
            //customer is login
            if(!isset($customerId)){
                $customerId = null;
                $tokenExist = $this->customerTokenCollection->create()
                    ->addFieldToFilter('customer_id',array('null' => true))
                    ->addFieldToFilter('guest_id',$deviceId)
                    ->addFieldToFilter('store_id', $this->storeManage->getStore()->getId());
            }
            else{
                $tokenExist = $this->customerTokenCollection->create()
                    ->addFieldToFilter('customer_id',$customerId)
                    ->addFieldToFilter('guest_id',$deviceId)
                    ->addFieldToFilter('store_id', $this->storeManage->getStore()->getId());
            }

            //if the customer has ever registered
            if(count($tokenExist) ){
                $currentToken = $tokenExist->getFirstItem();
                if(!$currentToken->getIsActive()){
                    //make sure only current web visitors receive the notification
                    $this->setActiveToken($customerId,$deviceId);
                    $currentToken->setIsActive(CustomerTokenModel::IS_ACTIVE);
                    $this->customerTokenResource->save($currentToken);
                }
                //if the token is refreshed
                if($token !== $currentToken->getToken()){
                    $currentToken->setData('token',$token);
                    $this->customerTokenResource->save($currentToken);
                }
                //set active for this session
            }
            //if token not exits, save new token
            else{
                $data['guest_id'] = $deviceId;
                $now = $this->dateTime->date()->format('Y-m-d');
                $data['created_at'] = $now;
                $data['token'] = $token;
                $data['store_id'] = $this->storeManage->getStore()->getId();
                $data['is_active'] = CustomerTokenModel::IS_ACTIVE;

                if(isset($customerId)) {
                    $data['customer_id'] = $customerId;
                }

                $this->setActiveToken($customerId,$deviceId);
                $currentToken->addData($data);
                $this->customerTokenResource->save($currentToken);
            }
            $result[] = [
                'status'=>true,
                'message'=>__("Save token success")
            ];
        }
        catch (\Exception $exception){
            $this->logger->error($exception->getMessage());
            $result[] = [
                'status'=>false,
                'message'=>$exception->getMessage()
            ];
        }
        return $result;
    }

    /**
     * If there are multiple accounts signed in on the same device, only the last account is allowed to receive notifications
     * @param $customerId
     * @param $id
     */
    public function setActiveToken($customerId,$id){
        $tokenActive = $this->customerTokenCollection->create()->addFieldToFilter('guest_id',$id);
        foreach ($tokenActive->getItems() as $item){
            if($item->getCustomerId() == $customerId){
                $item->setIsActive(CustomerTokenModel::IS_ACTIVE);
            }else{
                $item->setIsActive(CustomerTokenModel::IS_NOT_ACTIVE);
            }
            try {
                $this->customerTokenResource->save($item);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
