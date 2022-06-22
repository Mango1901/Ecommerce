<?php
namespace Magenest\MobileApi\Api;

use Magento\Framework\Api\ExtensibleDataInterface;

interface ResetPasswordInterface extends ExtensibleDataInterface
{
    /**
     * Get new password token
     *
     * @return string
     */
    public function getToken();

    /**
     * Set password token
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken( string $token);

    /**
     * Get customer email
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set customer email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail( string $email);

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus( string $status);
}
