<?php
namespace Magenest\PhotoReview\Observer\Order;

use Magenest\PhotoReview\Helper\Data;
use Magento\Catalog\Model\Product;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\Observer;
use Magenest\PhotoReview\Model\ReminderEmail;

class Reminder implements \Magento\Framework\Event\ObserverInterface
{
    /** @var \Magenest\PhotoReview\Model\ReminderEmailFactory  */
    protected $_reminderEmailModel;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail  */
    protected $_reminderEmailResource;

    /** @var Data  */
    protected $_helperData;

    protected $_logger;

    /** @var \Magento\Email\Model\TemplateFactory $_emailTemplate */
    protected $_emailTemplate;

    protected $storeId = 0;

    /** @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory  */
    protected $_reviewsColFactory;

    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $_resource;

    /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface  */
    protected $timezoneInterface;

    /** @var \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory  */
    protected $_reviewDetailCollection;

    /**
     * Reminder constructor.
     *
     * @param \Magenest\PhotoReview\Model\ReminderEmailFactory $reminderEmailFactory
     * @param \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail $reminderEmailResource
     * @param Data $helperData
     * @param \Magento\Email\Model\TemplateFactory $emailTemplate
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
     * @param \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory $reviewDetailCollection
     */
    public function __construct(
        \Magenest\PhotoReview\Model\ReminderEmailFactory $reminderEmailFactory,
        \Magenest\PhotoReview\Model\ResourceModel\ReminderEmail $reminderEmailResource,
        \Magenest\PhotoReview\Helper\Data $helperData,
        \Magento\Email\Model\TemplateFactory $emailTemplate,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
        \Magenest\PhotoReview\Model\ResourceModel\ReviewDetail\CollectionFactory $reviewDetailCollection
    ){
        $this->_reminderEmailModel = $reminderEmailFactory;
        $this->_reminderEmailResource = $reminderEmailResource;
        $this->_helperData = $helperData;
        $this->_logger = $logger;
        $this->_emailTemplate = $emailTemplate;
        $this->_reviewsColFactory = $collectionFactory;
        $this->_resource = $resourceConnection;
        $this->timezoneInterface = $timezoneInterface;
        $this->_reviewDetailCollection = $reviewDetailCollection;
    }

    public function execute(Observer $observer)
    {
        $enableModule = $this->_helperData->isModuleEnable();
        $enableReminder = $this->_helperData->isEnableReminder();
        if($enableModule && $enableReminder){
            try{
                /** @var \Magento\Sales\Model\Order $order */
                $order = $observer->getEvent()->getOrder();
                $orderId = $order->getId();
                if($this->checkSendCoupon($orderId) && $order->getStatus() == 'complete'){
                    $this->storeId = $order->getStoreId();
                    $orderEmail = $order->getCustomerEmail();
                    $sendAfter = $this->_helperData->getScopeConfig(Data::SEND_AFTER);
                    $sendAfter = $sendAfter != "" ? $sendAfter : 0;
                    $nextDay = date('Y-m-d H:i:s', strtotime("+$sendAfter days"));
                    $content = $this->generateMail($order);
                    $customerName = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
                    $dataRaw = [
                        'order_id' => $orderId,
                        'status' => ReminderEmail::STATUS_QUEUE,
                        'date_send' => $nextDay,
                        'email' => $orderEmail,
                        'content' => $content['content'],
                        'store_id' => $this->storeId,
                        'subject' => $content['subject'],
                        'customer_name' => $customerName
                    ];
                    $reminderModel = $this->_reminderEmailModel->create();
                    $this->_reminderEmailResource->load($reminderModel,$orderId,'order_id');
                    if(!$reminderModel->getId()){
                        $reminderModel->setData($dataRaw);
                        $this->_reminderEmailResource->save($reminderModel);
                    }
                }
            }catch (\Exception $exception){
                $this->_logger->critical($exception->getMessage());
            }
        }
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    protected function generateMail($order)
    {
        try{
            $emailTemplateModel = $this->getTemplateInstance()->loadByConfigPath(Data::REMINDER_EMAIL_TEMPLATE);
            $itemsHtml          = "<table style='border-collapse: collapse;width: 100%;'>";
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($order->getAllVisibleItems() as $item){
                if ($item->getParentItemId()) {
                    continue;
                }
                /** @var \Magento\Catalog\Model\Product $product */
                $product = $item->getProduct();
                if (!$product) {
                    continue;
                }
                $productImageUrl = $product->getMediaGalleryImages() ? $product->getMediaGalleryImages()->getFirstItem()->getUrl() : null;
                $itemsHtml .= "<tr style='border: 1px solid #ddd;padding: 8px;'>
                                <td style='border: 1px solid #ddd;padding: 8px; text-align: center;'>
                                    <a href='" . $product->getProductUrl() . "' target='_blank'>
                                        <img src='" . $productImageUrl . "' height='150' width='120'>
                                    </a>
                                </td>
                                <td style='border: 1px solid #ddd;padding: 8px; text-align: center;'>
                                    <span class='product-name' style='margin: 5px auto;display: table;'>" . $product->getName() . "</span>
                                    <a href='".$product->getProductUrl()."'><button>".__('Get your review')."</button></a>
                                </td>
                           </tr>";
            }
            $itemsHtml .= "</table>";
            $customerName = $order->getCustomerFirstname()." ".$order->getCustomerLastname();
            $vars = [
                'cart_items' => $itemsHtml,
                'customer_name' => $customerName
            ];
            $emailTemplateModel->setTemplateText($emailTemplateModel->getTemplateText());
            $emailTemplateModel->setVars($vars);
            $emailTemplateModel->setDesignConfig([
                'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeId
            ]);
            $content = html_entity_decode($emailTemplateModel->getProcessedTemplate($vars));
            $subject = '';
            if ($emailTemplateModel->getSubject()) {
                $subject = $emailTemplateModel->getSubject();
            }
            $result = [
                'content' => $content,
                'subject' => $subject
            ];
            return $result;
        }catch (\Exception $exception){
            $this->_logger->critical($exception->getMessage());
            return null;
        }
    }

    /**
     * @return \Magento\Email\Model\Template
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTemplateInstance()
    {
        /** @var \Magento\Email\Model\Template $template */
        $template = $this->_emailTemplate->create();
        $template->setDesignConfig([
            'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
            'store' => $this->storeId
        ]);
        return $template;
    }

    /**
     * @param $orderId
     *
     * @return bool
     */
    private function checkSendCoupon($orderId)
    {
        /** @var \Magenest\PhotoReview\Model\ReviewDetail $reviewDetail */
        $reviewDetail = $this->_reviewDetailCollection->create()
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem();
        if($reviewDetail->getCustomId()){
            return false;
        }
        return true;
    }
}
