<?php
/**
 * Created by PhpStorm.
 * User: doanhcn2
 * Date: 15/03/2019
 * Time: 14:34
 */

namespace Magenest\Slider\Controller\Adminhtml\Slider;

use Magento\Backend\App\Action;

class GetCategoryInfo extends Action
{
    protected $categoryFactory;

    public function __construct(Action\Context $context, \Magento\Catalog\Model\CategoryFactory $categoryFactory)
    {
        $this->categoryFactory = $categoryFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $categoryModel = $this->categoryFactory->create();
        $categoryId = $this->getRequest()->getParam('categoryId');

        $categoryData = $categoryModel->load($categoryId);
        // TODO: Implement execute() method.
        $response = $this->resultFactory
            ->create(\Magento\Framework\Controller\ResultFactory::TYPE_JSON)
            ->setData([
                'status'  => true,
                'url'  => $categoryData->getUrl(),
                'imageUrl' => $categoryData->getData('image') ? '/pub/media/catalog/category/' . $categoryData->getData('image') : '',
                'title' => $categoryData->getName(),
                'desc' => $categoryData->getData('description'),
            ]);
        return $response;
    }
}