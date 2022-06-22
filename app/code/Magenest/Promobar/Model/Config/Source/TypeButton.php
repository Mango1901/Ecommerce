<?php

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Magenest\Promobar\Model\Config\Source;

use Magenest\Promobar\Model\ResourceModel\Button\CollectionFactory;

class TypeButton
{
    protected $collectionFactory;

    /**
     * TypeButton constructor.
     * @param CollectionFactory $collectionFactory
     */
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
        $buttonCollection = $this->collectionFactory->create();
        $options = [];
        $options += ['0' => __('---Select Button---')];
        foreach($buttonCollection as $button){
            if($button->getStatus()==1) {
                $options += [
                    json_encode($button->getData()) => __($button->getData('title')),
                ];
            }
        }
        return $options;
    }
}
