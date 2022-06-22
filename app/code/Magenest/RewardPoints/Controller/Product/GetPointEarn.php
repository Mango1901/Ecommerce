<?php
/**
 * Copyright © 2020 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Booking & Reservation extension
 * NOTICE OF LICENSE
 *
 * @author Magenest
 * @time: 17/03/2021 15:40
 */

namespace Magenest\RewardPoints\Controller\Product;

use Magenest\RewardPoints\Block\PointInfo;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;

class GetPointEarn extends Action
{
    /**
     * @var \Magenest\RewardPoints\Helper\Data
     */
    protected $_helper;

    protected $_pointInfoBlock;

    public function __construct(
        PointInfo $pointInfo,
        \Magenest\RewardPoints\Helper\Data $helper,
        Context $context
    ) {
        parent::__construct($context);
        $this->_helper = $helper;
        $this->_pointInfoBlock = $pointInfo;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        try {
            $pointEarn = $this->_helper->getPointFromProductPrice($this->getRequest()->getParam('final_price'), $this->getRequest()->getParam('product_id'));
        } catch (LocalizedException $e) {
            $this->messageManager->addException($e);
        } finally {
            $pointEarnHtml = '<span id="point-show1">
                                <strong>
                                    <p style="color: '. $this->_pointInfoBlock->getPointColor() . ' font-size: '. $this->_pointInfoBlock->getPointSize() .'px"> +' . $pointEarn . ' ' . htmlspecialchars($this->_pointInfoBlock->getPointUnit(),ENT_QUOTES) .'</p>
                                </strong>
                              </span';
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData(['point_earn' => $pointEarnHtml]);
            return $resultJson;
        }
    }
}