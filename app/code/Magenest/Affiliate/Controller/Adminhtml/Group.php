<?php


namespace Magenest\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Model\GroupFactory;

/**
 * Class Group
 * @package Magenest\Affiliate\Controller\Adminhtml
 */
abstract class Group extends AbstractAction
{
    /**
     * Group Factory
     *
     * @var GroupFactory
     */
    protected $_groupFactory;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * Group constructor.
     *
     * @param Context $context
     * @param GroupFactory $groupFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        GroupFactory $groupFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_coreRegistry = $coreRegistry;

        parent::__construct($context, $resultPageFactory, $coreRegistry);
    }

    /**
     * @return mixed
     */
    protected function _initGroup()
    {
        $groupId = (int)$this->getRequest()->getParam('id');
        /** @var \Magenest\Affiliate\Model\Group $group */
        $group = $this->_groupFactory->create();
        if ($groupId) {
            $group->load($groupId);
            if (!$group->getId()) {
                $this->messageManager->addError(__('This account no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('affiliate/account/index');

                return $resultRedirect;
            }
        }

        return $group;
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Affiliate::group');
    }
}
