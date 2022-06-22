<?php
namespace Magenest\PhotoReview\Ui\Component\Listing\Column\Reminder;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class ViewAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /** @var \Magento\Framework\UrlInterface  */
    protected $_urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuider,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ){
        $this->_urlBuilder = $urlBuider;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if(isset($dataSource['data']['items'])){
            foreach ($dataSource['data']['items'] as &$item) {
                $email = $item['email'];
                $item[$this->getData('name')]['edit'] = [
                    'href' => $this->_urlBuilder->getUrl(
                        'photoreview/reminder/preview',
                        ['id' => $item['id']]
                    ),
                    'target' => '_blank',
                    'label' => __('Preview'),
                    'hidden' => false,
                ];
                $item[$this->getData('name')]['delete'] = [
                    'href' => $this->_urlBuilder->getUrl(
                        'photoreview/reminder/delete',
                        ['id' => $item['id']]
                    ),
                    'label' => __('Delete'),
                    'confirm' => [
                        'title' => __('Delete %1', $email),
                        'message' => __('Are you sure you want to delete a %1 record?', $email)
                    ],
                    'hidden' => false,
                ];
            }
        }
        return $dataSource;
    }
}