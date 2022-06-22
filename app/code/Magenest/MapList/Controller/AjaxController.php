<?php
/**
 * Created by PhpStorm.
 * User: hiennq
 * Date: 9/16/16
 * Time: 20:25
 */

namespace Magenest\MapList\Controller;

use Magento\Backend\App\Action;

class AjaxController extends Action
{
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        return $this->resultJsonFactory->create()->setData(
            array(
            'a' => 'b'
            )
        );
    }
}
