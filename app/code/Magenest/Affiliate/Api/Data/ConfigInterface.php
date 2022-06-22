<?php


namespace Magenest\Affiliate\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;
use Magenest\Affiliate\Api\Data\Config\GeneralConfigInterface;

/**
 * Interface ConfigInterface
 * @api
 */
interface ConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const GENERAL = 'general';

    const ACCOUNT = 'account';

    const COMMISSION = 'commission';

    const WITHDRAW = 'withdraw';

    const EMAIL = 'email';

    const REFER = 'refer';

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\GeneralConfigInterface
     */
    public function getGeneral();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\GeneralConfigInterface $generalConfig
     *
     * @return mixed
     */
    public function setGeneral(\Magenest\Affiliate\Api\Data\Config\GeneralConfigInterface $generalConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\AccountConfigInterface
     */
    public function getAccount();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\AccountConfigInterface $accountConfig
     *
     * @return mixed
     */
    public function setAccount(\Magenest\Affiliate\Api\Data\Config\AccountConfigInterface $accountConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\CommissionConfigInterface
     */
    public function getCommission();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\CommissionConfigInterface $commissionConfig
     *
     * @return mixed
     */
    public function setCommission(\Magenest\Affiliate\Api\Data\Config\CommissionConfigInterface $commissionConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\WithdrawConfigInterface
     */
    public function getWithdraw();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\WithdrawConfigInterface $withdrawConfig
     *
     * @return mixed
     */
    public function setWithdraw(\Magenest\Affiliate\Api\Data\Config\WithdrawConfigInterface $withdrawConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\EmailConfigInterface
     */
    public function getEmail();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\EmailConfigInterface $emailConfig
     *
     * @return mixed
     */
    public function setEmail(\Magenest\Affiliate\Api\Data\Config\EmailConfigInterface $emailConfig);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\ReferConfigInterface
     */
    public function getRefer();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\ReferConfigInterface $referConfig
     *
     * @return mixed
     */
    public function setRefer(\Magenest\Affiliate\Api\Data\Config\ReferConfigInterface $referConfig);
}
