<?php

namespace Magenest\SalesPerson\Model\Order\Source;

class AssignedToSales extends AbstractSource
{
    /**
     * @inheritDoc
     */
    protected function conditionGetReasonForArea()
    {
        return true;
    }
}
