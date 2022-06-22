<?php

namespace Magenest\MapList\Controller\Adminhtml\Holiday;

use Magenest\MapList\Controller\Adminhtml\Holiday;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magenest\MapList\Model\HolidayFactory;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends Holiday
{
    protected $_filter;

    public function __construct(
        Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        HolidayFactory $holidayFactory,
        LoggerInterface $logger,
        Filter $filter
    ) {
        $this->_filter = $filter;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $holidayFactory, $logger);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->holidayFactory->create()->getCollection());
        $deletedHoliday = 0;

        foreach ($collection->getItems() as $holiday) {
            $holiday->delete();
            $deletedHoliday++;
        }

        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $deletedHoliday)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('maplist/*/index');
    }
}
