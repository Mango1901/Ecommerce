<?php

namespace Magenest\MapList\Controller\Adminhtml\Brand;

use Magenest\MapList\Controller\Adminhtml\Brand;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magenest\MapList\Model\BrandFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Brand
{
    protected $_filter;

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        BrandFactory $brandFactory,
        LoggerInterface $logger,
        Filter $filter
    ) {
        $this->_filter = $filter;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $brandFactory, $logger);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->brandFactory->create()->getCollection());
        $deletedBrand = 0;

        foreach ($collection->getItems() as $brand) {
            $brand->delete();
            $deletedBrand++;
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $deletedBrand)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('maplist/*/index');
    }
}
