<?php

namespace Magenest\AffiliateOpt\Plugin\Model\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class MetadataProvider
 * @package Magenest\AffiliateOpt\Plugin\Model\Export
 */
class MetadataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * MetadataProvider constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * @param \Magento\Ui\Model\Export\MetadataProvider $subject
     * @param callable $proceed
     * @param DocumentInterface $document
     * @param $fields
     * @param $options
     *
     * @return array
     */
    public function aroundGetRowData(
        \Magento\Ui\Model\Export\MetadataProvider $subject,
        callable $proceed,
        DocumentInterface $document,
        $fields,
        $options
    ) {
        $nameSpace = $this->request->getParam('namespace');
        $row = [];
        $result = $proceed($document, $fields, $options);

        if ($nameSpace === 'affiliate_reports_accounts_listing' || $nameSpace === 'affiliate_sales_listing') {
            foreach ($fields as $column) {
                $customAttribute = $document->getCustomAttribute($column);
                if ($customAttribute) {
                    if (isset($options[$column]) && $column !== 'period') {
                        $key = $customAttribute->getValue();
                        if (isset($options[$column][$key])) {
                            $row[] = $options[$column][$key];
                        } else {
                            $row[] = '';
                        }
                    } else {
                        $row[] = $customAttribute->getValue();
                    }
                }
            }
        }

        return !empty($row) ? $row : $result;
    }
}
