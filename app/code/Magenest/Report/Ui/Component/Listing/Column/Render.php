<?php
/**
 * Number
 *
 * @copyright Copyright © 2021 Khanh. All rights reserved.
 * @author    khanhthanhvh@gmail.com
 */

namespace Magenest\Report\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;


class Render  extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = []
    ) {
        $this->priceFormatter = $priceFormatter;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as $key => $item) {
                    $base_grand_total = $this->priceFormatter->convertAndFormat(
                        $item['base_grand_total'], true,PriceCurrencyInterface::DEFAULT_PRECISION
                    );
                    $grand_total = $this->priceFormatter->convertAndFormat(
                        $item['grand_total'], true,PriceCurrencyInterface::DEFAULT_PRECISION
                    );
                    $shipping_amount = $this->priceFormatter->convertAndFormat(
                        $item['shipping_amount'], true,PriceCurrencyInterface::DEFAULT_PRECISION
                    );
                    $subtotal = $this->priceFormatter->convertAndFormat(
                        $item['subtotal'], true,PriceCurrencyInterface::DEFAULT_PRECISION
                    );
                    $total_refunded = $this->priceFormatter->convertAndFormat(
                        $item['total_refunded'], true,PriceCurrencyInterface::DEFAULT_PRECISION
                    );
                    $dataSource['data']['items'][$key]['qty_ordered'] = (int)$item['qty_ordered'];
                    $dataSource['data']['items'][$key]['base_grand_total'] = $base_grand_total;
                    $dataSource['data']['items'][$key]['grand_total'] = $grand_total;
                    $dataSource['data']['items'][$key]['shipping_amount'] = $shipping_amount;
                    $dataSource['data']['items'][$key]['subtotal'] =  $subtotal;
                    $dataSource['data']['items'][$key]['customer_firstname'] = $item['customer_firstname'] . ' '. $item['customer_lastname'] ;
                    $dataSource['data']['items'][$key]['total_refunded'] = $total_refunded;

            }
        }
            return $dataSource;
    }
}