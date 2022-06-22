<?php
/**
 * Created by Magenest JSC.
 * Time: 9:41
 */

namespace Magenest\SocialLogin\Controller\Customer;

use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class Account
 * @package Magenest\SocialLogin\Controller\Customer
 */
class Account extends AbstractAccount implements HttpGetActionInterface
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $pageResult;

    /**
     * Account constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageResult
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $pageResult
    ) {
        parent::__construct($context);
        $this->pageResult = $pageResult;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $pageResult = $this->pageResult->create();
        $pageResult->getConfig()->getTitle()->set('My Social Accounts');
        return $pageResult;
    }
}
