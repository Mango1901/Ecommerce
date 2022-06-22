<?php

namespace Magenest\Promobar\Model\Config\Source;

use Magenest\Promobar\Model\ResourceModel\Promobar\CollectionFactory;

class SelectPromobar implements \Magento\Framework\Option\ArrayInterface
{
    protected $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $barCollection = $this->collectionFactory->create();
        $options = [];
        $options += ['0' => __('--Select Promo Bar--')];
        foreach($barCollection as $bar){
            if($bar->getStatus()==0) {
                $options += [
                    $bar->getData('promobar_id') => __($bar->getData('title')),
                ];
            }
        }
        return $options;
    }
}
