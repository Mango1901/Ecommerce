<?php
namespace Magenest\MobileApi\Model;

use Magento\Framework\Model\AbstractExtensibleModel;
use Magenest\MobileApi\Api\ResetPasswordInterface;

class ResetPassword extends AbstractExtensibleModel implements ResetPasswordInterface
{
    /**
     * Get new password token
     *
     * @return string
     */
    public function getToken(){
        return $this->getData('token');
    }

    /**
     * Set new password token
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken( string $token) {
        return $this->setData('token', $token);
    }

    /**
     * Get customer email
     *
     * @return string
     */
    public function getEmail() {
        return $this->getData('email');
    }

    /**
     * Set customer email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail( string $email) {
        return $this->setData('email', $email);
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus() {
        return $this->getData('status');
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus( string $status) {
        return $this->setData('status', $status);
    }
}
