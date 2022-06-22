<?php

namespace Magenest\RewardPoints\Block\Widget\Grid\Massaction;
class Extended extends \Magento\Backend\Block\Widget\Grid\Massaction\Extended
{
    /**
     * Retrieve JSON string of selected checkboxes
     *
     * @return string
     */
    public function getSelectedJson()
    {
        return implode(',', $this->getData('assigned_customer') ?? []);
    }

    /**
     * Retrieve array of selected checkboxes
     *
     * @return string[]
     */
    public function getSelected()
    {
        return $this->getData('assigned_customer');
    }
}