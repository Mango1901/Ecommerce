<?php

namespace Magenest\StoreCredit\Model\Action;

use Magenest\StoreCredit\Model\Action;

class ConvertKpointKcoin extends Action
{
    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return __('Convert From Kpoint To Kcoin');
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return __('Convert Kpoint Kcoin');
    }
}
