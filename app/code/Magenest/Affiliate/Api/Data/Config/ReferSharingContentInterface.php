<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReferSharingContentInterface
 * @api
 */
interface ReferSharingContentInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const SUBJECT = 'subject';

    const EMAIL_CONTENT = 'email_content';

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setSubject($value);

    /**
     * @return string
     */
    public function getEmailContent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setEmailContent($value);
}
