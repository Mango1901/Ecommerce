<?php

namespace Magenest\SellOnInstagram\Controller\Adminhtml\Mapping;

/**
 * Class NewAction
 * @package Magenest\SellOnInstagram\Controller\Adminhtml\Mapping
 */
class NewAction extends AbstractMapping
{
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
