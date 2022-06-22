<?php

namespace Magenest\RewardPoints\Model\System\Config\Backend;

class ShowInHomePage extends \Magento\Framework\App\Config\Value
{
    public function isValueChanged()
    {
        $isValueChanged = parent::isValueChanged();
        if ($isValueChanged) {
            $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Layout::TYPE_IDENTIFIER);
            $this->cacheTypeList->invalidate(\Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER);
        }

        return $isValueChanged;
    }

}
