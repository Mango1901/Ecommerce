<?php
namespace Magenest\PhotoReview\Helper;

use Magento\Framework\App\Helper\Context;
use Psr\Log\LoggerInterface;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var \Magento\Framework\Translate\Inline\StateInterface  */
    protected $inlineTranslation;

    /** @var \Magento\Framework\Mail\Template\TransportBuilder  */
    protected $transportBuilder;

    /** @var LoggerInterface  */
    protected $logger;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Helper\Context $context
    ){
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->logger = $logger;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function sendEmail($templateId, $templateVars, $sender, $recipient)
    {
        $status = true;
        try{
            $this->inlineTranslation->suspend();
            $transport = $this->transportBuilder->setTemplateIdentifier(
                $templateId
            )->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
            ])->setTemplateVars(
                $templateVars
            )->setFrom(
                $sender
            )->addTo(
                $recipient['email'],
                $recipient['name']
            )->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
            $status = false;
        }
        return $status;
    }
}