<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_MegaMenu extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_MegaMenu
 */

namespace Magenest\MegaMenu\Controller\Adminhtml\Menu;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Magenest\MegaMenu\Model\MegaMenuFactory;
use Magento\Framework\View\Result\PageFactory;
use Magenest\MegaMenu\Model\MenuEntityFactory;
use Magento\Framework\Controller\ResultFactory;
use Magenest\MegaMenu\Controller\Adminhtml\Menu;

/**
 * Class MassDelete
 * @package Magenest\MegaMenu\Controller\Adminhtml\Menu
 */
class MassDelete extends Menu
{
    protected $_filter;

    public function __construct(
        Action\Context $context,
        PageFactory $pageFactory,
        MegaMenuFactory $megaMenuFactory,
        MenuEntityFactory $menuEntityFactory,
        Registry $registry,
        Filter $filter
    ) {
        $this->_filter = $filter;
        parent::__construct($context, $pageFactory, $megaMenuFactory, $menuEntityFactory, $registry);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_megaMenuFactory->create()->getCollection());
        $deletedGallery = 0;
        $model = $this->_objectManager->create('Magenest\MegaMenu\Model\MegaMenu');
        /** @var \Magenest\MegaMenu\Model\MegaMenu $item */
        if ($collection) {
            foreach ($collection->getItems() as $item) {
                $model->load($item['menu_id'])->delete();
                $deletedGallery++;
            }
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $deletedGallery)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('menu/*/');
    }
}
