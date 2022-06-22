<?php
namespace Magenest\NotificationBox\Controller\Adminhtml\NotificationType;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class MassDelete extends AbstractMassAction
{
    protected function massAction(AbstractCollection $collection)
    {
        $count = 0;
        $listNotificationTypeCantDelete = [];
        foreach ($collection->getItems() as $item) {
            if ($this->checkAssign($item)) {
                $listNotificationTypeCantDelete [] = $item->getEntityId();
                continue;
            }
            $this->notificationTypeResource->delete($item);
            $count++;
        }
        if (count($listNotificationTypeCantDelete)) {
            $this->messageManager->addWarningMessage(__('Can\'t delete notification(s) type(s) with id %1 because there are notification(s) that are using it', implode(", ", $listNotificationTypeCantDelete)));
        }
        $this->messageManager->addSuccessMessage(__('Total of %1 record(s) have been deleted.', $count));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($this->redirectUrl);
        return $resultRedirect;
    }
}
