<?php
namespace Magenest\Popup\Ui\Component\Listing\Column\Log;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class Data extends \Magento\Ui\Component\Listing\Columns\Column {

    protected $options;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ){
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $content = json_decode($item['content'],true);
                if(is_array($content)){
                    $result = '';
                    $count = 0;
                    foreach ($content as $raw){
                        if($count == 0){
                            $count++;
                            continue;
                        }
                        if(isset($raw['name'])){
                            $result .= $raw['name'].": ".$raw['value']."| ";
                        }
                    }
                    $item['content'] = $result != '' ? $result : $content;
                }
            }
        }
        return $dataSource;
    }
}