<?php


namespace Magenest\Affiliate\Api\Data\Config;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface ReferConfigInterface
 * @api
 */
interface ReferConfigInterface extends ExtensibleDataInterface
{
    /**
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENABLE = 'enable';

    const ACCOUNT_SHARING = 'account_sharing';

    const DEFAULT_LINK = 'default_link';

    const SHARING_CONTENT = 'sharing_content';

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
    public function getAccountSharing();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAccountSharing($value);

    /**
     * @return string
     */
    public function getDefaultLink();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultLink($value);

    /**
     * @return \Magenest\Affiliate\Api\Data\Config\ReferSharingContentInterface
     */
    public function getSharingContent();

    /**
     * @param \Magenest\Affiliate\Api\Data\Config\ReferSharingContentInterface $referSharingContent
     *
     * @return \Magenest\Affiliate\Api\Data\Config\ReferSharingContentInterface
     */
    public function setSharingContent(\Magenest\Affiliate\Api\Data\Config\ReferSharingContentInterface $referSharingContent);
}
