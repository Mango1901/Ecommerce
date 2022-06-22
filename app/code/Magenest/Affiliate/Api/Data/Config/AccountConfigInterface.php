<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface AccountConfigInterface
 * @api
 */
interface AccountConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SIGN_UP_DEFAULT_GROUP = 'sign_up_default_group';

    const SIGN_UP_ADMIN_APPROVED = 'sign_up_admin_approved';

    const SIGN_UP_DEFAULT_EMAIL_NOTIFICATION = 'sign_up_default_email_notification';

    const TERM_CONDITIONS_ENABLE = 'term_conditions_enable';

    const TERM_CONDITIONS_CHECKBOX_TEXT = 'term_conditions_checkbox_text';

    const TERM_CONDITIONS_TITLE = 'term_conditions_title';

    const TERM_CONDITIONS_HTML = 'term_conditions_html';

    const TERM_CONDITIONS_DEFAULT_CHECKBOX = 'term_conditions_default_checkbox';

    const BALANCE_LIMIT = 'balance_limit';

    const BALANCE_NEGATIVE = 'balance_negative';

    /**
     * @return int
     */
    public function getSignUpDefaultGroup();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSignUpDefaultGroup($value);

    /**
     * @return int
     */
    public function getSignUpAdminApproved();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSignUpAdminApproved($value);

    /**
     * @return int
     */
    public function getSignUpDefaultEmailNotification();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSignUpDefaultEmailNotification($value);

    /**
     * @return bool
     */
    public function getTermConditionsEnable();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setTermConditionsEnable($value);

    /**
     * @return string
     */
    public function getTermConditionsCheckboxText();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTermConditionsCheckboxText($value);

    /**
     * @return string
     */
    public function getTermConditionsTitle();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTermConditionsTitle($value);

    /**
     * @return string
     */
    public function getTermConditionsHtml();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTermConditionsHtml($value);

    /**
     * @return bool
     */
    public function getTermConditionsDefaultCheckbox();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setTermConditionsDefaultCheckbox($value);

    /**
     * @return float
     */
    public function getBalanceLimit();

    /**
     * @param float $value
     *
     * @return $this
     */
    public function setBalanceLimit($value);

    /**
     * @return bool
     */
    public function getBalanceNegative();

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function setBalanceNegative($value);
}
