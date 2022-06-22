<?php
namespace Magenest\NotificationBox\Controller\Customer;
use Magento\Framework\App\Action\Action;
use Magenest\NotificationBox\Helper\Helper;
use Magento\Framework\App\Action\Context;
use Magenest\NotificationBox\Model\CustomerTokenFactory;
use Magenest\NotificationBox\Model\CustomerToken as CustomerTokenModel;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken;
use Magenest\NotificationBox\Model\ResourceModel\CustomerToken\CollectionFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\AlreadyExistsException;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Framework\Stdlib\DateTime\TimezoneInterface as DateTime;
use Psr\Log\LoggerInterface;
use \Magento\Checkout\Model\Session;

class SaveToken extends Action
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

    /** @var Session  */
    protected $checkoutSession;

    /**
     * @param Context $context
     * @param Helper $helper
     * @param CustomerTokenFactory $customerTokenFactory
     * @param CustomerToken $customerToken
     * @param JsonFactory $resultJsonFactory
     * @param CollectionFactory $customerTokenCollection
     * @param StoreManagerInterface $storeManage
     * @param DateTime $dateTime
     * @param LoggerInterface $logger
     * @param Session $checkoutSession
     */
    public function __construct(
        Context $context,
        Helper $helper,
        CustomerTokenFactory $customerTokenFactory,
        CustomerToken  $customerToken,
        JsonFactory $resultJsonFactory,
        CollectionFactory $customerTokenCollection,
        StoreManagerInterface $storeManage,
        DateTime $dateTime,
        LoggerInterface $logger,
        Session $checkoutSession
    )
    {
        $this->checkoutSession                  = $checkoutSession;
        $this->logger                   = $logger;
        $this->dateTime                 = $dateTime;
        $this->storeManage              = $storeManage;
        $this->customerTokenCollection  = $customerTokenCollection;
        $this->helper                   = $helper;
        $this->customerTokenFactory     = $customerTokenFactory;
        $this->customerTokenResource    = $customerToken;
        $this->resultJsonFactory        = $resultJsonFactory;
        parent::__construct($context);
    }

    /** save customer Token */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $params = $this->getRequest()->getParams();
        $token =  isset($params['token'])?$params['token']:null;
        $this->checkoutSession->setTokenData($token);
        if($token){
            //save token;
            try {
                $customerId = $this->helper->getCustomerId();
                $customerName = $this->helper->getCustomerName();
                $currentToken = $this->customerTokenFactory->create();
                //customer is login
                if(!isset($customerId)){
                    $customerId = null;
                    $tokenExist = $this->customerTokenCollection->create()
                        ->addFieldToFilter('customer_id',array('null' => true))
                        ->addFieldToFilter('guest_id',$params['id'])
                        ->addFieldToFilter('store_id', $this->storeManage->getStore()->getId());
                }
                else{
                    $tokenExist = $this->customerTokenCollection->create()
                        ->addFieldToFilter('customer_id',$customerId)
                        ->addFieldToFilter('guest_id',$params['id'])
                        ->addFieldToFilter('store_id', $this->storeManage->getStore()->getId());;
                }

                //if the customer has ever registered
                if(count($tokenExist) ){
                    $this->customerTokenResource->load($currentToken,$tokenExist->getFirstItem()->getId());
                    if(!$currentToken->getIsActive()){
                        //make sure only current web visitors receive the notification
                        $this->setActiveToken($customerId,$params['id']);
                        $currentToken->setIsActive(CustomerTokenModel::IS_ACTIVE);
                        $this->customerTokenResource->save($currentToken);
                    }
                    //if the token is refreshed
                    if($params['token'] !== $tokenExist->getFirstItem()->getToken()){
                        $this->customerTokenResource->load($currentToken,$tokenExist->getFirstItem()->getId());
                        $currentToken->setData('token',$params['token']);
                        $this->customerTokenResource->save($currentToken);
                    }
                    //set active for this session
                }
                //if token not exits, save new token
                else{
                    $data['guest_id'] = $params['id'];
                    $now = $this->dateTime->date()->format('Y-m-d');
                    $data['created_at'] = $now;
                    $data['token'] = $token;
                    $data['store_id'] = $this->storeManage->getStore()->getId();
                    $data['is_active'] = CustomerTokenModel::IS_ACTIVE;

                    if(isset($customerId)) {
                        $data['customer_id'] = $customerId;
                    }

                    if(isset($customerName) && $customerName != " ") {
                        $data['customer_name'] = $customerName;
                    }
                    else{
                        $data['customer_name'] = 'Guest';
                    }
                    $this->setActiveToken($customerId,$params['id']);
                    $currentToken->addData($data);
                    $this->customerTokenResource->save($currentToken);
                }
            }
            catch (\Exception $exception){
                    return $result->setData("false");
            }
        }
        return $result->setData("success");
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
