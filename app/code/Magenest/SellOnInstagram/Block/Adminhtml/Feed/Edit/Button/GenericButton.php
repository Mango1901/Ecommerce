<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed\Edit\Button;

use Exception;
use Psr\Log\LoggerInterface;
use Magento\Backend\Block\Widget\Context;
use Magenest\SellOnInstagram\Model\InstagramFeedFactory;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed;

class GenericButton
{
    /**
     * @var InstagramFeedFactory
     */
    protected $feedFactory;
    /**
     * @var Context
     */
    protected $context;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var InstagramFeed
     */
    protected $feedResource;

    public function __construct(
        InstagramFeedFactory $feedFactory,
        InstagramFeed $feedResource,
        Context $context,
        LoggerInterface $logger
    )
    {
        $this->feedFactory = $feedFactory;
        $this->feedResource = $feedResource;
        $this->context = $context;
        $this->logger = $logger;
    }

    /**
     * @return int|null
     */
    public function getFeedId()
    {
        try {
            $id = $this->context->getRequest()->getParam('id');
            $feedModel = $this->feedFactory->create();
            $this->feedResource->load($feedModel, $id);
            $feedId = $feedModel->getId();
        } catch (Exception $exception) {
            $this->logger->debug($exception->getMessage());
        }
        return $feedId ?? null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     *
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
