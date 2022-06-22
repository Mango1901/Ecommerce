<?php

namespace Magenest\PhotoReview\Plugin\Controller\Adminhtml\Review;

use Magento\Review\Controller\Adminhtml\Product;

/**
 * Class Save
 */
class Save
{
    /**
     * Change post value of title from form to save database
     *
     * @param Product $subject
     * @return array
     */
    public function beforeExecute(Product $subject)
    {
        if ($data = $subject->getRequest()->getPostValue()) {
            if (isset($data['title']) && is_array($data['title'])) {
                $valueTitleNew = implode(',', array_values($data['title']));
                $subject->getRequest()->setPostValue('title', $valueTitleNew);
            }
        }
        return [];
    }
}
