<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_Kangaroo extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\OrderManagement\Ui\Component\Listing\Column\ConfirmedPersonName;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Escaper;

class Options implements OptionSourceInterface
{
    /**
     * @var Escaper
     */
    private $escaper;

    protected $userCollection;

    /**
     * Constructor
     *
     * @param Escaper $escaper
     * @param \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollection
     */
    public function __construct(
        Escaper $escaper,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollection
    ) {
        $this->userCollection = $userCollection;
        $this->escaper = $escaper;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->userCollection->create() as $user) {
            $result[] = [
                'value' => $user->getId(),
                'label' => $this->escaper->escapeHtml($user->getName())
            ];
        }

        return $result;
    }
}
