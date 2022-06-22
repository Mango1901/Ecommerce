<?php
namespace Magenest\NotificationBox\Model;

use Magento\Framework\Model\AbstractModel;

class CustomerToken extends AbstractModel
{
    const STATUS_SUBSCRIBED = 1;
    const STATUS_UNSUBSCRIBED = 0;
    const STATUS_SUBSCRIBED_LABEL = 'Subscribed';
    const STATUS_UNSUBSCRIBED_LABEL = 'Unsubscribed';
    const IS_ACTIVE = 1;
    const IS_NOT_ACTIVE = 0;
    protected function _construct()
    {
        $this->_init('Magenest\NotificationBox\Model\ResourceModel\CustomerToken');
    }
    public function updateMultiRecord(){
        $allCustomerToken = $this->getCollection();
        foreach ($allCustomerToken as $token) {
            try {
                $tokenModel = $this->customerTokenFactory->create();
                $this->customerTokenResource->load($tokenModel, $token->getEntityId());
                $tokenModel->setData('limit', 0);
                $this->customerTokenResource->save($tokenModel);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
}
