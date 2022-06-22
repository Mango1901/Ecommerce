<?php
namespace Magenest\OrderCancel\Model\Order\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use Psr\Log\LoggerInterface;

/**
 * Class abstract source option for cancel reason
 */
abstract class AbstractSource implements OptionSourceInterface
{
    const CANCEL_REASON_OPTIONS_PATH = 'order_cancel/cancel_reason/cancel_reason_options';

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var Json */
    protected $json;

    /** @var LoggerInterface */
    protected $logger;

    /**
     * CancelReason constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Json $json
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Json $json,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
        $this->logger = $logger;
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

        try {
            $reasonOptions = $this->scopeConfig->getValue(
                self::CANCEL_REASON_OPTIONS_PATH,
                ScopeInterface::SCOPE_STORE
            );

            if (!empty($reasonOptions)) {
                foreach ($this->json->unserialize($reasonOptions) as $key => $value) {
                    if (isset($value['apply_to']) && $this->conditionGetReasonForArea($value['apply_to'])) {
                        $options[$key] = __($value['cancel_reason_option']);
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->logger->error($exception->getMessage());
        }

        return $options;
    }

    /**
     * Abstract function conditionGetReasonForArea
     * Used for get option for area frontend or admin
     * @param string|null $value
     * @return bool
     */
    abstract protected function conditionGetReasonForArea($value);
}
