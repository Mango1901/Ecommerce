<?php
namespace Magenest\CustomizePdf\Plugin;

use Magenest\CustomizePdf\Helper\Constant;

/**
 * Class ConvertPdfUploadToDataSheet
 * @package Magenest\CustomizePdf\Plugin
 */
class ConvertPdfUploadToDataSheet
{
    /**
     * @param \Magento\Catalog\Model\Product\TypeTransitionManager $subject
     * @param $result
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     * @throws \Exception
     */
    public function afterProcessProduct(
        \Magento\Catalog\Model\Product\TypeTransitionManager $subject,
        $result,
        \Magento\Catalog\Model\Product $product

    ) {
        if($product->getData('product_instruction_pdf')) {
            try {
                $pdfUpload = $product->getData('product_instruction_pdf');
                if (is_array($pdfUpload)) {
                    $pdfUploadFile = reset($pdfUpload);
                    $filePath = $pdfUploadFile['file_path'] ?? null;
                    if ($filePath) {
                        $product->setData('product_instruction_pdf', $filePath);
                    }
                } else {
                    $product->setData('product_instruction_pdf', null);
                }
            } catch (\Exception $exception) {
                throw new \Exception($exception->getMessage());
            }
        }
        return $result;
    }
}
