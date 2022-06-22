<?php

namespace Magenest\OrderCancel\Block\Adminhtml\Form\Field;

use Magento\Framework\View\Element\Html\Select;

class ApplyToColumn extends Select
{
    const APPLY_TO_BOTH = '0';
    const APPLY_TO_FRONTEND = '1';
    const APPLY_TO_ADMIN = '2';

    /**
     * Set "name" for <select> element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set "id" for <select> element
     *
     * @param $value
     * @return $this
     */
    public function setInputId($value)
    {
        return $this->setId($value);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    public function _toHtml(): string
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->getSourceOptions());
        }
        return parent::_toHtml();
    }

    /**
     * @return array
     */
    private function getSourceOptions(): array
    {
        return [
            ['label' => 'Both', 'value' => self::APPLY_TO_BOTH],
            ['label' => 'Frontend', 'value' => self::APPLY_TO_FRONTEND],
            ['label' => 'Admin Dashboard', 'value' => self::APPLY_TO_ADMIN],
        ];
    }
}
