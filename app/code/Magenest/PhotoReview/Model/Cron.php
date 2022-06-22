<?php
namespace Magenest\PhotoReview\Model;

use Magenest\PhotoReview\Helper\Data;
use Magento\Framework\Stdlib\DateTime\Filter\DateTime;
use Magenest\PhotoReview\Model\ReminderEmail as ReminderEmailModel;

class Cron
{
    /** @var \Magento\Framework\App\ProductMetadataInterface  */
    protected $productMetadata;

    protected $versionMagento = null;

    /** @var \Magenest\PhotoReview\Helper\Data  */
    protected $_helperData;

    /** @var \Psr\Log\LoggerInterface  */
    protected $logger;

    /** @var \Magento\Sales\Model\OrderFactory  */
    protected $orderFactory;

    /** @var \Magenest\PhotoReview\Model\ReminderEmailFactory  */
    protected $_reminderEmailModel;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail  */
    protected $_reminderEmailResource;

    /** @var ResourceModel\ReminderEmail\CollectionFactory  */
    protected $_reminderEmailCollection;

    protected $_transportBuilder = null;

    /** @var \Magento\Framework\Translate\Inline\StateInterface  */
    protected $inlineTranslation;

    protected $magentoVersion = 0;

    public function __construct(
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magenest\PhotoReview\Model\ReminderEmailFactory $reminderEmailFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail $reminderEmailResource,
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail\CollectionFactory $reminderEmailCollection,
        \Magenest\PhotoReview\Model\Mail\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ){
        $this->productMetadata = $productMetadata;
        $this->_reminderEmailModel = $reminderEmailFactory;
        $this->_reminderEmailResource = $reminderEmailResource;
        $this->_helperData = $helperData;
        $this->logger = $logger;
        $this->orderFactory = $orderFactory;
        $this->_reminderEmailCollection = $reminderEmailCollection;
        $this->inlineTranslation = $inlineTranslation;
    }

    public function execute()
    {
        try{
            $isEnable = $this->_helperData->getScopeConfig(Data::ENABLE_EMAIL_REMINDER);
            if($isEnable){
                $now = date('Y-m-d H:i:s');
                $collections = $this->_reminderEmailCollection->create()
                    ->addFieldToFilter('status', ReminderEmail::STATUS_QUEUE)
                    ->addFieldToFilter('date_send', ['lteq' => $now])
                    ->getItems();
                $ids = [];
                foreach ($collections as $collection){
                    $this->sendEmail($collection->getData());
                    $ids[] = $collection->getId();
                }
                $this->_reminderEmailResource->updateMultiStatus($ids, ReminderEmailModel::STATUS_SENT);
            }
        }catch (\Exception $exception){
            $this->logger->critical($exception->getMessage());
        }
    }

    protected function getVersionMagento()
    {
        if($this->versionMagento == null){
            $this->versionMagento = $this->productMetadata->getVersion();
        }
        return $this->versionMagento;
    }

    protected function getTransportBuilder()
    {
        if($this->_transportBuilder == null){
            $objectManager   = \Magento\Framework\App\ObjectManager::getInstance();
            $this->versionMagento = $this->getVersionMagento();
            if(version_compare($this->versionMagento, '2.3.3') < 0){
                $transportBuilder = $objectManager->get(\Magenest\PhotoReview\Model\Mail\TransportBuilder::class);
            }else{
                $transportBuilder = $objectManager->get(\Magenest\PhotoReview\Model\Mail\TransportBuilderV2::class);
            }
            $this->_transportBuilder = $transportBuilder;
        }
        return $this->_transportBuilder;
    }
    public function sendEmail($data)
    {
        try{
            $from = $this->_helperData->getScopeConfig(Data::REMINDER_EMAIL_SENDER);
            $content = $data['content'];
            $subject = $data['subject'];
            $storeId = $data['store_id'];
            $email = $data['email'];
            $name = $data['customer_name'];
            $transportBuilder = $this->getTransportBuilder();
            $transportBuilder->setMessageContent(htmlspecialchars_decode($content),$subject,$from,$this->versionMagento);
            $this->inlineTranslation->suspend();
            $transport = $transportBuilder->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId,
                ]
            )->setTemplateVars([])
            ->setFrom($from)
            ->addTo($email)
            ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        }catch (\Exception $exception){
            $this->logger->critical($exception->getMessage());
        }
    }
}