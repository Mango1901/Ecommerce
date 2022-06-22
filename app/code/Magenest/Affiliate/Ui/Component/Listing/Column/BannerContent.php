<?php


namespace Magenest\Affiliate\Ui\Component\Listing\Column;

use Exception;
use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class BannerContent
 * @package Magenest\Affiliate\Ui\Component\Listing\Column
 */
class BannerContent extends Column
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param FilterProvider $filterProvider
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FilterProvider $filterProvider,
        array $components = [],
        array $data = []
    ) {
        $this->filterProvider = $filterProvider;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     * @throws Exception
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item['content_html'] = $this->filterProvider->getPageFilter()->filter($item['content']);
            }
        }

        return $dataSource;
    }
}
