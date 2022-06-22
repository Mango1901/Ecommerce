<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 11/06/2022
 * Time: 09:05
 */
declare(strict_types=1);

namespace Magenest\Customer\Plugin\Block\Adminhtml\Order\Create\Form;

use Magenest\Customer\Helper\ConfigHelper;
use Magento\Framework\Data\Form;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\Create\Form\Address as AddressMagento;

class Address
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(
        ConfigHelper $configHelper
    ) {
        $this->configHelper = $configHelper;
    }

    /**
     * After get Form
     *
     * @param AddressMagento $subject
     * @param Form $form
     * @return Form
     * @throws LocalizedException
     */
    public function afterGetForm(
        AddressMagento $subject,
        Form           $form
    ) {
        if ($this->configHelper->isEnabledFullNameInstead()) {
            $form->getElement('main')
                ->removeField('prefix')
                ->removeField('middlename')
                ->removeField('fullname')
                ->removeField('suffix')
                ->removeField('lastname')
                ->removeField('firstname')
                ->addField(
                    'fullname',
                    'text',
                    [
                        'name' => $form->getHtmlNamePrefix() ? $form->getHtmlNamePrefix() . '[fullname]' : 'fullname',
                        'label' => __('Full Name'),
                        'title' => __('Full Name'),
                        'class' => 'required-entry maximum-length-500 minimum-length-1',
                        'required' => true,
                        'value' => $this->getValueForFullName($subject->getFormValues())
                    ],
                    '^'
                );
        } else {
            $form->getElement('main')
                ->removeField('fullname');
        }

        return $form;
    }

    /**
     * @param array $formValues
     * @return mixed|string
     */
    private function getValueForFullName($formValues)
    {
        if (isset($formValues['fullname'])) {
            return $formValues['fullname'];
        }

        if (
            isset($formValues['lastname']) &&
            isset($formValues['firstname'])
        ) {
            return $formValues['lastname'] . ' ' . $formValues['firstname'];
        }

        return '';
    }
}
