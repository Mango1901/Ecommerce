<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_tn233 extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_tn233
 */

namespace Magenest\CategoryIcon\Controller\Adminhtml\Category\Image;

use Exception;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class CategoryIcon extends Upload
{
    /**
     * Upload file controller action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        try {
            $result           = $this->imageUploader->saveFileToTmpDir('category_icon');
            $result = $this->getResult($result);
        } catch (Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
