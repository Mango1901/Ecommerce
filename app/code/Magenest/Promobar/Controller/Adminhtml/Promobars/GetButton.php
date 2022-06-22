<?php

namespace Magenest\Promobar\Controller\Adminhtml\Promobars;

use Magento\Framework\Controller\ResultFactory;


class GetButton extends \Magento\Backend\App\Action
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core file storage database
     *
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $coreFileStorageDatabase;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    protected $_button;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magenest\Promobar\Model\ButtonFactory $buttonCollection,
        \Psr\Log\LoggerInterface $logger
    )
    {
        parent::__construct($context);
        $this->logger = $logger;
        $this->_button = $buttonCollection;
    }
    public function execute()
    {
        $idButton = $this->getRequest()->getParam('id');
            $data = [];
            $button = $this->_button->create()->load($idButton);
            $editButton = json_decode($button->getEditButton(), true);
            $data += ['data' => $button->getData()];
            $data += ['edit_button' => $editButton];
            return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($data);
    }
}