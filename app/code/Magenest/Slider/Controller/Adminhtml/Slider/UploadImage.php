<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 07/03/2019
 * Time: 10:03
 */

namespace Magenest\Slider\Controller\Adminhtml\Slider;


use Magento\Backend\App\Action;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;

class UploadImage extends Action
{
    protected $imageUploader;

    public function __construct(Action\Context $context, ImageUploader $imageUploader)
    {
        parent::__construct($context);
        $this->imageUploader = $imageUploader;
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
        // TODO: Implement execute() method.
        try {
            $result = $this->imageUploader->saveFileToTmpDir('bgImage');
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}