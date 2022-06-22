<?php


namespace Magenest\Slider\Controller\Adminhtml\Slider;


use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Setup\Exception;

class CreatePreview extends Action
{
    protected $sliderPreviewFactory;

    /** @var JsonFactory  */
    protected $_resultJsonFactory;

    public function __construct(
        \Magenest\Slider\Model\SliderPreviewFactory $sliderPreviewFactory,
        JsonFactory $resultJsonFactory,
        Action\Context $context
    )
    {
        $this->sliderPreviewFactory = $sliderPreviewFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
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
        try {
            $result = ['error' => true, 'token' => 222];

            $requestData = json_decode($this->getRequest()->getParam('preview_data'), true);
            $previewData = [];
            $sliderId = $requestData[1]['value'];
            $sliderConfig = $requestData[2]['value'];
            $slider = $requestData[3]['value'];
            $childSlider = $requestData[5]['value'];
            $sliderPreviewModel = $this->sliderPreviewFactory->create();
            $key_id = sha1(rand(1, 9999999));
            if ($sliderId) {
                $previewCollection = $sliderPreviewModel->getCollection()->addFieldToFilter('slider_id', $sliderId);

                if (count($previewCollection->getItems())){
                    $previewID = $previewCollection->getFirstItem()['preview_id'];
                    $sliderPreviewModel->load($previewID);
                    $previewData['preview_id'] = $previewID;
                    $key_id = $previewCollection->getFirstItem()['key_id'];
                }
                $previewData['slider_id'] = $sliderId;
            }

            $sliderPreviewModel->setData(array_merge($previewData, [
                'key_id' => $key_id,
                'config' => $sliderConfig,
                'slider_data' => $slider,
                'childSlider' => $childSlider
            ]));

            // Save slider
            $sliderPreviewModel->save();
            $result = [
                'error' => false,
                'token' => $key_id,
            ];

        } catch (CouldNotSaveException $e) {
            $result['message'] = $e->getMessage();
        }
        return $this->_resultJsonFactory->create()->setData($result);
    }
}