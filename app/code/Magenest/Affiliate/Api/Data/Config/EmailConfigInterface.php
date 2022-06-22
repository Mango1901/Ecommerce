<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface EmailConfigInterface
 * @api
 */
interface EmailConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SENDER = 'sender';

    const ADMIN = 'admin';

    const ACCOUNT = 'account';

    const TRANSACTION = 'transaction';

    const WITHDRAW = 'withdraw';

    /**
     * @return string
     */
    public function getSender();

    /**
     * @param $value
     *
     * @return mixed
     */
    public function setSender($value);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\EmailAdminConfigInterface
     */
    public function getAdmin();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\EmailAdminConfigInterface $emailAdminConfig
     *
     * @return $this
     */
    public function setAdmin(\Magenest\Affiliate\Api\Data\Config\EmailAdminConfigInterface $emailAdminConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\EmailAccountConfigInterface
     */
    public function getAccount();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\EmailAccountConfigInterface $emailAccountConfig
     *
     * @return $this
     */
    public function setAccount(\Magenest\Affiliate\Api\Data\Config\EmailAccountConfigInterface $emailAccountConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\EmailTransactionConfigInterface
     */
    public function getTransaction();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\EmailTransactionConfigInterface $emailTransactionConfig
     *
     * @return $this
     */
    public function setTransaction(\Magenest\Affiliate\Api\Data\Config\EmailTransactionConfigInterface $emailTransactionConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface
     */
    public function getWithdraw();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface $emailWithdrawConfig
     *
     * @return $this
     */
    public function setWithdraw(\Magenest\Affiliate\Api\Data\Config\EmailWithdrawConfigInterface $emailWithdrawConfig);
}
