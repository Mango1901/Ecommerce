<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 28/03/2019
 * Time: 16:30
 */

namespace Magenest\Slider\Controller\Adminhtml\Slider;


use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Cache\Frontend\Pool as FrontendPool;

class Delete extends \Magenest\Slider\Controller\Adminhtml\Slider
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

    public function __construct(
        Action\Context $context,
        \Magento\Widget\Model\Widget\InstanceFactory $widgetFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magenest\Slider\Model\SliderFactory $sliderFactory,
        TypeListInterface $cacheTypeList,
        FrontendPool $cacheFrontendPool
    )
    {
        $this->sliderFactory = $sliderFactory;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_cacheFrontendPool = $cacheFrontendPool;

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
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->sliderFactory->create();
                $model->load($id);
                $position = json_decode($model->getData('position'), true);
                $widgetInstance = $this->_initWidgetInstance(null, $position['widget_id']);
                $widgetInstance->delete();
                $model->delete();
                $this->messageManager->addSuccessMessage(__('You deleted the Slider.'));
                $this->_cacheTypeList->cleanType('config');
                foreach ($this->_cacheFrontendPool as $cacheFrontend) {
                    $cacheFrontend->getBackend()->clean();
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Slider to delete.'));
        return $resultRedirect->setPath('*/*/');
    }
}