<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\CustomTableRate\Controller\Adminhtml\System\Config;

use Magenest\CustomTableRate\Block\Adminhtml\Grid;
use Magento\Config\Controller\Adminhtml\System\ConfigSectionChecker;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Config\Controller\Adminhtml\System\AbstractConfig;
use Magento\Backend\App\Action\Context;
use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Export
 * @package Magenest\CustomTableRate\Controller\Adminhtml\System\Config
 */
class Export extends AbstractConfig
{
    /**
     * @var FileFactory
     */
    protected $_fileFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Constructor.
     *
     * @param Context $context
     * @param Structure $configStructure
     * @param ConfigSectionChecker $sectionChecker
     * @param FileFactory $fileFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Structure $configStructure,
        ConfigSectionChecker $sectionChecker,
        FileFactory $fileFactory,
        StoreManagerInterface $storeManager
    ) {
        $this->_storeManager = $storeManager;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context, $configStructure, $sectionChecker);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $website = $this->_storeManager->getWebsite($this->getRequest()->getParam('website'));
        /** @var $gridBlock \Magenest\CustomTableRate\Block\Adminhtml\Grid */
        $gridBlock = $this->_view->getLayout()->createBlock(Grid::class);
        $gridBlock->setWebsiteId($website->getId())->setMethod($this->getRequest()->getParam('method'));

        return $this->_fileFactory->create('kangaroo_tablerates.csv', $gridBlock->getCsvFile(), DirectoryList::VAR_DIR);
    }
}
