<?php

namespace Magenest\NotifyOrderComment\Plugin\Order;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class HistoryResourceModelPlugin
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * HistoryResourceModelPlugin constructor.
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->session = $session;
    }

    /**
     * Function Before Save Plugin
     *
     * @param AbstractDb $subject
     * @param AbstractModel $object
     * @return array
     */
    public function beforeSave(AbstractDb $subject, AbstractModel $object)
    {
        if ($object->isObjectNew()) {
            if ($user = $this->session->getUser()) {
                $userId = $user->getId();
                $object->setUserId($userId);
            }
        }
        return [ $object ];
    }
}
