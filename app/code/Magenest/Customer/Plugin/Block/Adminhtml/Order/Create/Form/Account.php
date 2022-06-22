<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * User: leo
 * Date: 11/06/2022
 * Time: 10:28
 */
declare(strict_types=1);

namespace Magenest\Customer\Plugin\Block\Adminhtml\Order\Create\Form;

use Magento\Framework\Data\Form;
use Magento\Sales\Block\Adminhtml\Order\Create\Form\Account as AccountMagento;

class Account
{
    /**
     * @param AccountMagento $subject
     * @param Form $result
     * @return Form
     */
    public function afterGetForm(AccountMagento $subject, Form $result): Form
    {
        $result->getElement('fullname')
            ->addClass('required-entry')
            ->setRequired(true);
        return $result;
    }
}
