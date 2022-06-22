<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Controller\Adminhtml\Preview;

use Magento\Backend\App\Action;
use Magento\Framework\Math\Random;
use Magenest\MegaMenu\Model\PreviewFactory;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Create
 * @package Magenest\MegaMenu\Controller\Adminhtml\Preview
 */
class Create extends Action
{
    /** @var JsonFactory */
    protected $_resultJsonFactory;

    /** @var Random */
    protected $_mathRandom;

    /** @var PreviewFactory */
    protected $_previewFactory;

    /**
     * Constructor.
     *
     * @param Action\Context $context
     * @param JsonFactory $resultJsonFactory
     * @param PreviewFactory $previewFactory
     * @param Random $random
     */
    public function __construct(
        Action\Context $context,
        JsonFactory $resultJsonFactory,
        PreviewFactory $previewFactory,
        Random $random
    ) {
        $this->_previewFactory = $previewFactory;
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_mathRandom = $random;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $result = ['error' => true, 'chan' => 222];

        $request = $this->getRequest();
        if ($request->getParam('isAjax')) {
            try {
                $preview = $this->_previewFactory->create()->addData([
                    'token' => $this->_mathRandom->getUniqueHash(),
                    'data' => $request->getParam('preview_data')
                ])->save();

                $result = [
                    'error' => false,
                    'token' => $preview->getToken(),
                ];
            } catch (\Exception $e) {
                $result['message'] = $e->getMessage();
            }

            return $this->_resultJsonFactory->create()->setData($result);
        }

        return null;
    }
}
