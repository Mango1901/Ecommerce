<?php
namespace Magenest\CouponCode\Plugin;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\SalesRule\Model\Rule\DataProvider;

class Images
{
    /**
     * @var Json
     */
    protected $serialize;

    /**
     * Data constructor
     *
     * @param Json $serialize
     */
    public function __construct(
        Json $serialize
    ) {
        $this->serialize = $serialize;
    }

    /**
     * Handle images
     *
     * @param DataProvider $subject
     * @param array $result
     * @return array
     */
    public function afterGetData(DataProvider $subject, $result)
    {
        if (!empty($result)) {
            foreach ($result as $value) {
                $id = $value['rule_id'];
            }
            if (!empty($result[$id]['images'])) {
                $result[$id]['images'] = $this->serialize->unserialize($value['images']);

            }
        }
        return $result;
    }
}
