<?php


namespace Magenest\Affiliate\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Magenest\Affiliate\Model\TransactionFactory;

/**
 * Class Transaction
 * @package Magenest\Affiliate\Controller\Adminhtml
 */
abstract class Transaction extends AbstractAction
{
    /**
     * @var TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * Transaction constructor.
     *
     * @param Context $context
     * @param TransactionFactory $transactionFactory
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        TransactionFactory $transactionFactory,
        Registry $coreRegistry,
        PageFactory $resultPageFactory
    ) {
        $this->_transactionFactory = $transactionFactory;

        parent::__construct($context, $resultPageFactory, $coreRegistry);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_Affiliate::transaction');
    }
}
