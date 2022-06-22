<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\AdvancedReports\Controller\Adminhtml\Reports;

class Index extends \Aheadworks\AdvancedReports\Controller\Adminhtml\Reports
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $aclUrls = [
            ['acl' => 'Aheadworks_AdvancedReports::reports_salesoverview', 'url' => '*/salesoverview/index'],
            ['acl' => 'Aheadworks_AdvancedReports::reports_productperformance', 'url' => '*/productperformance/index'],
        ];

        $redirectUrl = $aclUrls[0]['url'];
        foreach ($aclUrls as $item) {
            if (!$this->_authorization->isAllowed($item['acl'])) {
                continue;
            }
            $redirectUrl = $item['url'];
            break;
        }
        $redirectResult = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $redirectResult->setUrl(
            $this->getUrl($redirectUrl, ['_current' => true])
        );
        return $redirectResult;
    }
}
