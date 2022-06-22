<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 10/12/16
 * Time: 09:40
 */

namespace Magenest\MapList\Controller\Adminhtml\Holiday;

use Magenest\MapList\Controller\Adminhtml\Holiday;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Grid
 *
 * @package Magenest\MapList\Controller\Adminhtml\Holiday
 */
class Grid extends Holiday
{

    protected $resultRawFactory;
    protected $layoutFactory;

    /**
     * @return \Magento\Framework\Controller\Result\Raw|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Magenest\MapList\Model\HolidayFactory $holidayFactory,
        LoggerInterface $logger,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $resultPageFactory,
            $holidayFactory,
            $logger
        );
    }

    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Magenest\MapList\Block\Adminhtml\Holiday\Edit\Tab\AddStore\Grid',
                'location.tab.list'
            )->toHtml()
        );
    }
}
