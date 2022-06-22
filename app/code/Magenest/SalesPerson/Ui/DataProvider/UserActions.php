<?php

namespace Magenest\SalesPerson\Ui\DataProvider;

use Magento\Framework\UrlInterface;

use Magento\User\Model\ResourceModel\User\CollectionFactory;

class UserActions
{
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * UserActions constructor.
     * @param CollectionFactory $collectionFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(CollectionFactory $collectionFactory, UrlInterface $urlBuilder)
    {
        $this->collectionFactory = $collectionFactory;

        $this->urlBuilder = $urlBuilder;
    }

    public function getActions()
    {
        $actions = [];

        $users = $this->collectionFactory->create();

        foreach ($users as $user) {
            if ($user->getData("role_name") == "Sale" && $user->getData("is_salesperson") == 1 && $user->getData("is_active") == 1) {
                $actions[] = [
                    'label' => $user->getName(),
                    'url' => $this->urlBuilder->getUrl('salesperson/order/assignedtosales', ['user_id' => $user->getId()]),
                ];
            }
        }

        return $actions;
    }
}
