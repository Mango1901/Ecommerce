<?php
namespace Magenest\NotificationBox\Controller\Adminhtml\Notification;

use Magenest\NotificationBox\Model\Notification;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class ChangeNotificationStatus extends AbstractMassAction
{

    protected function massAction(AbstractCollection $collection)
    {
        $count = 0;
        foreach ($collection->getItems() as $item) {
            if($item->getIsActive()){
                $item->setIsActive(Notification::NOT_ACTIVE);
            }
            else{
                $item->setIsActive(Notification::ACTIVE);
            }
            $this->notificationResource->save($item);
            $count++;
        }
        $this->messageManager->addSuccessMessage(__('Total of %1 record(s) were modified.', $count));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->redirectUrl);
        return $resultRedirect;
    }
}
