<?php

namespace Magenest\SalesPerson\Ui\Component\Listing\MassActions;

use Magento\Framework\View\Element\UiComponent\ContextInterface;

class DynamicAction extends \Magento\Ui\Component\Action
{
    public function __construct(ContextInterface $context, array $components = [], array $data = [], $actions = null)
    {
        parent::__construct($context, $components, $data, $actions);
    }

    public function prepare()
    {
        $config = $this->getData('config');

        if (isset($config['action_resource'])) {
            $this->actions = $config['action_resource']->getActions();
        }

        parent::prepare();
    }
}
