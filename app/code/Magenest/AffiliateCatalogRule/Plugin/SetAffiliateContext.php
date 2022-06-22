<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 28/10/2021
 * Time: 14:43
 */

namespace Magenest\AffiliateCatalogRule\Plugin;

use Magenest\Affiliate\Model\Account\Status;
use Magenest\Affiliate\Model\AccountFactory;
use Magenest\AffiliateCatalogRule\Helper\Constant;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Http\Context as HttpContext;

class SetAffiliateContext
{
    /** @var Session */
    protected $customerSession;

    /** @var HttpContext */
    protected $httpContext;

    /** @var AccountFactory */
    protected $accountFactory;

    /**
     * @param Session $customerSession
     * @param HttpContext $httpContext
     * @param AccountFactory $accountFactory
     */
    public function __construct(Session $customerSession, HttpContext $httpContext, AccountFactory $accountFactory)
    {
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->accountFactory = $accountFactory;
    }

    public function beforeExecute()
    {
        if ($this->customerSession->isLoggedIn()) {
            $currentAffiliate = $this->accountFactory->create()->load(
                $this->customerSession->getCustomerId(),
                'customer_id'
            );
            $this->httpContext->setValue(
                Constant::IS_AFFILIATE_CONTEXT,
                (bool)($currentAffiliate->getId() && $currentAffiliate->getStatus() == Status::ACTIVE),
                false
            );
        }
    }
}
