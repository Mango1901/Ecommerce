<?php

namespace Magenest\MapList\Controller\Adminhtml\Location;

use Magenest\MapList\Controller\Adminhtml\Location;
use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magenest\MapList\Model\LocationFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Location
{
    protected $_filter;

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        LocationFactory $locationFactory,
        LoggerInterface $logger,
        Filter $filter
    ) {
        $this->_filter = $filter;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $locationFactory, $logger);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->locationFactory->create()->getCollection());
        $deletedLocation = 0;

        foreach ($collection->getItems() as $location) {
            $location->delete();
            $deletedLocation++;
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $deletedLocation)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('maplist/*/index');
    }
}
