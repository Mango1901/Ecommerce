<?php

namespace Magenest\CustomerAvatar\Plugin\CustomerData;

use Magenest\CustomerAvatar\Setup\Patch\Data\ProfilePictureAttribute;
use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Helper\View;
use Magenest\CustomerAvatar\Block\Attributes\Avatar;

/**
 * Class Customer
 */
class Customer
{
    /**
     * @var CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var View
     */
    protected $customerViewHelper;

    /**
     * @var Avatar
     */
    protected $customerAvatar;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param View $customerViewHelper
     * @param Avatar $customerAvatar
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        View $customerViewHelper,
        Avatar $customerAvatar
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->customerViewHelper = $customerViewHelper;
        $this->customerAvatar = $customerAvatar;
    }

    /**
     * @inheirtDoc
     */
    public function afterGetSectionData(\Magento\Customer\CustomerData\Customer $subject, $result)
    {
        if (!$this->currentCustomer->getCustomerId()) {
            return [];
        }

        $customer = $this->currentCustomer->getCustomer();

        if (!empty($customer->getCustomAttribute(ProfilePictureAttribute::PROFILE_PICTURE_CODE))) {
            $file = $customer->getCustomAttribute(ProfilePictureAttribute::PROFILE_PICTURE_CODE)->getValue();
        } else {
            $file = '';
        }

        return array_merge(
            $result,
            [
                'avatar' => $this->customerAvatar->getAvatarCurrentCustomer($file)
            ]
        );
    }
}
