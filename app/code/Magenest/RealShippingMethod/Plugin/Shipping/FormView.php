<?php
namespace Magenest\RealShippingMethod\Plugin\Shipping;

class FormView
{
    /**
     * @return string
     *
     * Remove Create Shipping Label button.
     * Shipping Label will be created with other function, not depend on customer's selected shipping method.
     */
    public function afterGetCreateLabelButton()
    {
        return "";
    }
}
