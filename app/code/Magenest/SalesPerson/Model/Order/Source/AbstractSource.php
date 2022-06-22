<?php

namespace Magenest\SalesPerson\Model\Order\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\User\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;

/**
 * Class AdminCancelReason
 */
abstract class AbstractSource implements OptionSourceInterface
{
    /** @var Json */
    protected $json;

    /**
     * @var UserCollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * CancelReason constructor.
     * @param UserCollectionFactory $userCollectionFactory
     * @param Json $json
     */
    public function __construct(
        UserCollectionFactory $userCollectionFactory,
        Json $json
    ) {
        $this->userCollectionFactory = $userCollectionFactory;
        $this->json = $json;
    }

    /**
     * @param $value
     * @return mixed|string
     */
    public function getOptionText($value)
    {
        $options = static::getAllOptions();
        foreach ($options as $key => $option) {
            if ($key == $value) {
                return $option;
            }
        }

        return "";
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $allOptions = $this->getAllOptions();
        $result = [];
        foreach ($allOptions as $value => $label) {
            $result [] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getAllOptions()
    {
        $options = [];
        $users = $this->userCollectionFactory->create();
        try {
            foreach ($users as $user) {
                if ($user->getData("role_name") == "Sale" && $user->getData("is_salesperson") == 1 && $user->getData("is_active") == 1) {
                    $options[$user->getId()] = $user->getName();
                }
            }
        } catch (\Exception $exception) {
        }

        return $options;
    }

    abstract protected function conditionGetReasonForArea();
}
