<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/17/16
 * Time: 11:37
 */

namespace Magenest\MapList\Controller;

use Magento\Framework\App\Action\Action;

abstract class DefaultController extends Action
{
    protected $resultPageFactory;
    protected $resultForwardFactory;
    protected $_logger;
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magenest\MapList\Model\LocationFactory $locationFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        $this->_logger = $loggerInterface;
        $this->_locationFactory = $locationFactory;
        parent::__construct($context);
    }
}
