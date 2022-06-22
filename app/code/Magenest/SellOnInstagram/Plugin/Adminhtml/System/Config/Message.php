<?php

namespace Magenest\SellOnInstagram\Plugin\Adminhtml\System\Config;
class Message
{
    const NO_ERROR = 0;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->messageManager = $messageManager;
    }

    public function beforeExecute(\Magento\Config\Controller\Adminhtml\System\Config\Edit $subject)
    {
        $errorsMess = $subject->getRequest()->getParam('errorMess');
        $mess = $subject->getRequest()->getParam('message');
        if (isset($errorsMess)) {
            if ($errorsMess == self::NO_ERROR) {
                $this->messageManager->addSuccessMessage(__('Get Access Token Successfully!'));
            } else {
                if (!empty($mess)) {
                    $this->messageManager->addErrorMessage(__($mess));
                }
            }
        }
    }
}

