<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 28/03/2019
 * Time: 16:47
 */

namespace Magenest\Slider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool as FrontendPool;
use Magento\Ui\Component\MassAction\Filter;

class MassDelete extends \Magenest\Slider\Controller\Adminhtml\Slider
{
    protected $sliderFactory;

    /**
     * @var TypeListInterface
     */
    protected $_cacheTypeList;
    /**
     * @var FrontendPool
     */
    protected $_cacheFrontendPool;
    protected $_filter;

    public function __construct(
        Action\Context $context,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\Slider\Model\SliderFactory $sliderFactory,
        TypeListInterface $cacheTypeList,
        FrontendPool $cacheFrontendPool,
        Filter $filter
    )
    {
        $this->sliderFactory = $sliderFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;
        $this->_filter = $filter;

        parent::__construct($context, $widgetFactory, $logger);
    }

    /**
     * Execute action based on request and return result
     *
     * Note: Request will be added as operation argument in future
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->_filter->getCollection($this->sliderFactory->create()->getCollection());
        $numSliderDelete = 0;
        foreach ($collection->getItems() as $sliderDelete) {
            try{
                $position = json_decode($sliderDelete->getData('position'), true);
                $widgetInstance = $this->_initWidgetInstance(null, $position['widget_id']);
                $widgetInstance->delete();
                $sliderDelete->delete();
                $numSliderDelete++;
            }catch (\Exception $e){
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->messageManager->addSuccessMessage(__('You have successfully deleted.'));
        $this->_cacheTypeList->cleanType('config');
        foreach ($this->_cacheFrontendPool as $cacheFrontend) {
            $cacheFrontend->getBackend()->clean();
        }
        return $resultRedirect->setPath('*/*/');
    }
}