<?php
namespace Magenest\NotificationBox\Controller\Adminhtml\Register;
use Magenest\NotificationBox\Model\CustomerToken;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Unsubscribe extends AbstractMassAction
{
    protected function massAction(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getItems() as $item) {
            if($item->getStatus()){
                $item->setData('status',CustomerToken::STATUS_UNSUBSCRIBED);
                $this->customerTokenResource->save($item);
                $count++;
            }
        }
        $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been unsubscribe.', $count));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->redirectUrl);
        return $resultRedirect;
    }
}
