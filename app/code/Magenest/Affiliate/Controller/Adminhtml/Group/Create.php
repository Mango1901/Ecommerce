<?php


namespace Magenest\Affiliate\Controller\Adminhtml\Group;

use Magento\Framework\View\Result\Page;
use Magenest\Affiliate\Controller\Adminhtml\Group;

/**
 * Class Create
 * @package Magenest\Affiliate\Controller\Adminhtml\Group
 */
class Create extends Group
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page|Page
     */
    public function execute()
    {
        /** @var \Magenest\Affiliate\Model\Group $group */
        $group = $this->_initGroup();
        $data = $this->_getSession()->getData('affiliate_group_data', true);
        if (!empty($data)) {
            $group->setData($data);
        }
        $this->_coreRegistry->register('current_group', $group);

        /** @var \Magento\Backend\Model\View\Result\Page|Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('Magenest_Affiliate::group');
        $resultPage->getConfig()->getTitle()->set(__('Groups'));

        $resultPage->getConfig()->getTitle()->prepend(__('New Group'));

        return $resultPage;
    }
}
