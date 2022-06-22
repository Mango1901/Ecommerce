<?php

namespace Magenest\RewardPoints\Controller\Adminhtml\Transaction;

use Magenest\RewardPoints\Model\ResourceModel\Transaction;
use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Magenest\RewardPoints\Controller\Customer
 */
class Save extends Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magenest\RewardPoints\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var Transaction
     */
    protected $_transactionResource;

    /**
     * @var \Magento\Backend\Model\SessionFactory
     */
    protected $sessionFactory;

    /**
     * Save constructor.
     * @param Transaction $transactionResource
     * @param \Magenest\RewardPoints\Helper\Data $help
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param Action\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magenest\RewardPoints\Model\TransactionFactory $transactionFactory
     * @param \Magento\Backend\Model\SessionFactory $sessionFactory
     */
    public function __construct(
        Transaction $transactionResource,
        \Magenest\RewardPoints\Helper\Data $help,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Action\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magenest\RewardPoints\Model\TransactionFactory $transactionFactory,
        \Magento\Backend\Model\SessionFactory $sessionFactory
    )
    {
        $this->helper            = $help;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->authSession = $authSession;
        $this->transactionFactory = $transactionFactory;
        $this->_transactionResource = $transactionResource;
        $this->sessionFactory = $sessionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $success = true;
        $resultJson = $this->_resultJsonFactory->create();
        try {
            $pointAfter = $this->addPointToCustomer(
                $this->getRequest()->getParam('customer_id'),
                $this->getRequest()->getParam('points_change'),
                $this->getRequest()->getParam('comment')
            );

            if ($pointAfter === 0) {
                return $resultJson->setData([
                    'success'=>$success,
                    'depleted'=>true,
                ]);
            }
        } catch (\Exception $e) {
            $this->messageManager->addException($e);
            $success = false;
        }

        return $resultJson->setData(['success'=>$success]);
    }

    /**
     * @param $customerId
     * @param $pointChange
     * @param $comment
     * @return mixed|bool|int
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    protected function addPointToCustomer($customerId, $pointChange, $comment)
    {
        if (!empty($customerId) && !empty($pointChange)) {
            $model = $this->transactionFactory->create();
            $model->setData([
                'customer_id' => $customerId,
                'rule_id' => -1,
                'points_change' => $pointChange,
                'comment' => $comment
            ]);
            $this->_transactionResource->save($model);

            $pointAfter = $this->helper->addPointsAccount($model->getCustomerId(), $model->getPointsChange(), $model->getId());

            $model->setData('points_after', $pointAfter);
            $ruleId = $model->getRuleId();
            if ($ruleId == -1) {
                $auth = $this->authSession->getUser();
                $firstNameAdmin = $auth->getData('firstname');
                $comment = 'Admin' . '(' . $firstNameAdmin . ')' . ' - ' . $model->getComment();
                $model->setData('comment', $comment);
            }
            $this->_transactionResource->save($model);

            return $pointAfter;
        }

        return false;
    }

}
