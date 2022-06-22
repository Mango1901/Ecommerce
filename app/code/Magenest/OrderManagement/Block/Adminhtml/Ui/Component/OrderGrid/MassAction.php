<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\OrderManagement\Block\Adminhtml\Ui\Component\OrderGrid;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\AbstractComponent;
use Magenest\OrderManagement\Helper\Authorization;

/**
 * Class Authorization
 * @package Magenest\OrderManagement\Block\Adminhtml\Ui\Component\OrderGrid
 */
class MassAction extends AbstractComponent
{
    /** @const */
    const NAME = 'massaction';

    /**
     * @var Authorization
     */
    protected $_authorization;

    /**
     * Constructor.
     *
     * @param ContextInterface $context
     * @param Authorization $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        Authorization $authorization,
        array $components = [],
        array $data = []
    )
    {
        $this->_authorization = $authorization;
        parent::__construct($context, $components, $data);
    }

    /**
     * @inheritDoc
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        foreach ($this->getChildComponents() as $component) {
            $componentConfig = $component->getConfiguration();
            $disabledAction = $componentConfig['actionDisable'] ?? false;
            $url = $componentConfig['url'] ?? (
                isset($componentConfig['actions']) ?
                    array_values($componentConfig['actions'])[0]['url'] :
                    null
                );

            if ($disabledAction || !$this->_authorization->allowAccessToUrl($url)) {
                continue;
            }

            $config['actions'][] = $componentConfig;
        }

        $origConfig = $this->getConfiguration();
        if ($origConfig !== $config) {
            $config = array_replace_recursive($config, $origConfig);
        }

        $this->setData('config', $config);
        $this->components = [];

        parent::prepare();
    }

    /**
     * @inheritDoc
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}
