<?php

namespace Magenest\AffiliateOpt\Model\Import\Behavior;

use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Source\Import\AbstractBehavior;

/**
 * Import behavior source model used for defining the behaviour during the import.
 *
 * @api
 * @since 100.0.2
 */
class Transaction extends AbstractBehavior
{
    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            Import::BEHAVIOR_ADD_UPDATE => __('Add'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return 'mp_affiliate_transaction';
    }
}
