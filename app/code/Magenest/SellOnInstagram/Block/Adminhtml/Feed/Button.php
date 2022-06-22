<?php

namespace Magenest\SellOnInstagram\Block\Adminhtml\Feed;

use Exception;
use Magenest\SellOnInstagram\Model\InstagramFeedFactory;
use Magenest\SellOnInstagram\Model\ResourceModel\InstagramFeed;

class Button extends \Magento\Backend\Block\Widget\Container
{
    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;
    /**
     * @var InstagramFeedFactory
     */
    protected $feedFactory;
    /**
     * @var InstagramFeed
     */
    protected $feedResource;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        InstagramFeedFactory $feedFactory,
        InstagramFeed $feedResource,
        array $data = []
    )
    {
        $this->context = $context;
        $this->feedFactory = $feedFactory;
        $this->feedResource = $feedResource;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $buttonSync = [
            'id' => 'sync_now',
            'label' => __('Sync Now'),
            'class' => 'sync_now',
            'button_class' => '',
            'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options' => $this->_getCustomActionListOptions(),
        ];
        if ($this->getFeedId()) {
            $this->buttonList->add('sync_now', $buttonSync);
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'CustomActionList' split button
     *
     * @return array
     */
    protected function _getCustomActionListOptions()
    {
        /*list of button which you want to add*/
        $splitButtonOptions = [
            'syncCreateNow' => [
                'label' => __('Create & Update'),
                'onclick' => sprintf("location.href = '%s';", $this->getSyncCreateNowUrl())
            ],
            'syncDeleteNow' => [
                'label' => __('Delete'),
                'onclick' => sprintf("location.href = '%s';", $this->getSyncDeleteNowUrl())
            ]
        ];
        /* in above list you can also pass others attribute of buttons*/
        return $splitButtonOptions;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * @return string
     */
    public function getSyncCreateNowUrl()
    {
        return $this->getUrl('*/*/syncCreateNow', ['id' => $this->getFeedId(), 'edit_page' => true]);
    }

    /**
     * @return string
     */
    public function getSyncDeleteNowUrl()
    {
        return $this->getUrl('*/*/syncDeleteNow', ['id' => $this->getFeedId(), 'edit_page' => true]);
    }

    /**
     * @return mixed|null
     */
    public function getFeedId()
    {
        try {
            $id = $this->context->getRequest()->getParam('id');
            $feedModel = $this->feedFactory->create();
            $this->feedResource->load($feedModel, $id);
            $feedId = $feedModel->getId();
        } catch (Exception $exception) {
            $this->_logger->debug($exception->getMessage());
        }
        return $feedId ?? null;
    }
}
