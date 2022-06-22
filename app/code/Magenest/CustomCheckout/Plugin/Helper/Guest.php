<?php

namespace Magenest\CustomCheckout\Plugin\Helper;

use Magenest\CustomCheckout\Controller\Onepage\Success;
use Magenest\CustomCheckout\Model\ResourceModel\HashParam as HashParamResourceModel;
use Magento\Framework\App\RequestInterface;
use Magenest\CustomCheckout\Model\HashParam;
use Magenest\CustomCheckout\Model\HashParamFactory;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class Guest
 */
class Guest
{
    /**
     * @var HashParamFactory
     */
    private $hashParam;

    /**
     * @var HashParamResourceModel
     */
    private $hashParamResourceModel;

    /**
     * @var SerializerInterface
     */
    private $serialize;

    public function __construct(
        HashParamFactory $hashParam,
        HashParamResourceModel $hashParamResourceModel,
        SerializerInterface $serialize
    ) {
        $this->hashParam = $hashParam;
        $this->hashParamResourceModel = $hashParamResourceModel;
        $this->serialize = $serialize;
    }

    /**
     * Function beforeLoadValidOrder
     *
     * Plugin used for set post value to request from params
     *
     * @param \Magento\Sales\Helper\Guest $subject
     * @param RequestInterface $request
     * @return RequestInterface[]
     */
    public function beforeLoadValidOrder(
        \Magento\Sales\Helper\Guest $subject,
        RequestInterface $request
    ) {
        if ($hashKey = $request->getParam(Success::KEY_PARAM_HASH)) {
            /**
             * @var $hashParam HashParam
             */
            $hashParam = $this->hashParam->create();
            $this->hashParamResourceModel->load($hashParam, $hashKey, 'hash_key');
            $dataFromHash = $this->serialize->unserialize($hashParam->getHashValue());
            $request->setPostValue(
                    Success::PARAM_ORDER_GUEST[0], $dataFromHash[Success::PARAM_ORDER_GUEST[0]]
                )->setPostValue(
                    Success::PARAM_ORDER_GUEST[1], $dataFromHash[Success::PARAM_ORDER_GUEST[1]]
                )->setPostValue(
                    Success::PARAM_ORDER_GUEST[2], $dataFromHash[Success::PARAM_ORDER_GUEST[2]]
                )->setPostValue(
                    Success::PARAM_ORDER_GUEST[3], $dataFromHash[Success::PARAM_ORDER_GUEST[3]]
                // Value temp for 'oar_zip' used for pass $postData['oar_type'] error index in code core
                )->setPostValue(
                    Success::PARAM_ORDER_GUEST[4], 'temp'
                );
        }

        return [$request];
    }
}
