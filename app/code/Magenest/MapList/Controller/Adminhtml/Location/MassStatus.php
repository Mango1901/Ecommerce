<?php
/**
 * Created by PhpStorm.
 * User: heomep
 * Date: 24/09/2016
 * Time: 14:10
 */

namespace Magenest\MapList\Controller\Adminhtml\Location;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magenest\MapList\Model\LocationFactory;
use Magento\Framework\Exception\LocalizedException;

class MassStatus extends Action
{
    protected $_filter;

    protected $_locationFactory;


    public function __construct(
        Filter $filter,
        LocationFactory $locationFactory,
        Action\Context $context
    ) {
        $this->_filter = $filter;
        $this->_locationFactory = $locationFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $status = (int)$this->getRequest()->getParam('is_active');
        $collection = $this->_filter->getCollection($this->_locationFactory->create()->getCollection());
        $total = 0;

        try {
            foreach ($collection as $item) {
                $item->setData('is_active', $status)->save();
                $total++;
            }

            $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $total));
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('maplist/*/index');
    }
}
