<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Rule;

use Magenest\RewardPoints\Model\RuleFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magenest\RewardPoints\Controller\Adminhtml\Rule;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Delete
 * @package Magenest\RewardPoints\Controller\Adminhtml\Rule
 */
class Delete extends Rule
{
    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    protected $logger;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        RuleFactory $ruleFactory,
        Registry $registry,
        \Magento\Backend\Model\Session $session,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->session = $session;
        $this->logger = $logger;
        parent::__construct($context, $pageFactory, $ruleFactory, $registry);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($id) {
            $model = $this->_ruleFactory->create()->load($id);
            if ($id != $model->getId()) {
                throw new LocalizedException(__('Something is wrong while deleting the rule. Please try again.'));
            }
            $this->session->setPageData($model->getData());

            try {
                $model->delete();
                $this->messageManager->addSuccess(__('The rule has been successfully deleted.'));
                $this->session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/index');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->logger->critical($e);
                $this->session->setPageData($model->getData());

                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }

        return $resultRedirect->setPath('*/*/index');
    }
}
