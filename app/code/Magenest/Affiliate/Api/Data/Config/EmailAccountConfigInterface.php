<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EmailAccountConfigInterface
 * @api
 */
interface EmailAccountConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLE = 'enable';

    const WELCOME = 'welcome';

    const APPROVE = 'approve';

    const ENABLE_REJECTION = 'enable_rejection';

    const REJECTION_TEMPLATE = 'rejection_template';

    const ENABLE_STATUS = 'enable_status';

    const STATUS_TEMPLATE = 'status_template';

    const ENABLE_WITHDRAW_CANCEL = 'enable_withdraw_cancel';

    const WITHDRAW_CANCEL_TEMPLATE = 'withdraw_cancel_template';

    /**
     * @return bool
     */
    public function getEnable();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnable($value);

    /**
     * @return string
     */
    public function getWelcome();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setWelcome($value);

    /**
     * @return string
     */
    public function getApprove();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setApprove($value);

    /**
     * @return bool
     */
    public function getEnableRejection();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnableRejection($value);

    /**
     * @return string
     */
    public function getRejectionTemplate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRejectionTemplate($value);


    /**
     * @return bool
     */
    public function getEnableStatus();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnableStatus($value);

    /**
     * @return string
     */
    public function getStatusTemplate();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatusTemplate($value);

    /**
     * @return bool
     */
    public function getEnableWithdrawCancel();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setEnableWithdrawCancel($value);
}
