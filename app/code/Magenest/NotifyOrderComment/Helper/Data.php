<?php


namespace Magenest\NotifyOrderComment\Helper;


use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\User\Model\UserFactory;
use UnexpectedValueException;

/**
 * Class Data
 * @package Magenest\NotifyOrderComment\Helper
 */
class Data extends AbstractHelper
{
    CONST TEMPLATE_NOTIFY_ORDER_ID = 'notify_order_comment';

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var StateInterface
     */
    protected $_inlineTranslation;
    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $state
     * @param StoreManagerInterface $storeManager
     * @param AuthorizationInterface $_authorization
     * @param UserFactory $userFactory
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        StoreManagerInterface $storeManager,
        AuthorizationInterface $_authorization,
        UserFactory $userFactory
    ) {
        $this->transportBuilder   = $transportBuilder;
        $this->_inlineTranslation = $state;
        $this->_storeManager      = $storeManager;
        $this->_authorization = $_authorization;
        $this->userFactory = $userFactory;
        parent::__construct($context);
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    public function isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    public function getEmailDefaultConfig()
    {
        return $this->scopeConfig->getValue('notify_order_comment/setting/email_default');
    }

    public function sendEmailToAdmin($emailFirst, $emailArr, $statusHistory)
    {
        $templateId = self::TEMPLATE_NOTIFY_ORDER_ID;
        $varData    = [
            'status' => $statusHistory->getStatus(),
            'comment' => $statusHistory->getComment(),
            'order_id' => $statusHistory->getOrder()->getIncrementId()
        ];
        try {
            $this->_inlineTranslation->suspend();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions([
                    'area' => Area::AREA_FRONTEND,
                    'store' => Store::DEFAULT_STORE_ID
                ])->setTemplateVars(
                    $varData
                )->setFrom(
                    $this->getEmailSenderConfig() ?: 'sales'
                )->addCc($emailArr)->addTo(
                    $emailFirst
                )->getTransport();
            $transport->sendMessage();
            $this->_inlineTranslation->resume();
        } catch (MailException $e) {
            $this->_logger->critical($e->getMessage());
        } catch (UnexpectedValueException $e) {
            $this->_logger->critical($e->getMessage());
        } catch (LocalizedException $e) {
            $this->_logger->critical($e->getMessage());
        }
    }

    public function getEmailSenderConfig()
    {
        return $this->scopeConfig->getValue('notify_order_comment/setting/sender');
    }

    public function send($templateId, $varData, $email, $name = null)
    {

    }

    public function getNameByUserId($userId)
    {
        if ($userId) {
            $user = $this->userFactory->create()->load($userId);
            return $user->getName() ?? 'System';
        } else {
            return 'System';
        }
    }
}
