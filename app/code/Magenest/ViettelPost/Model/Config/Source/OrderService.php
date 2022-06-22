<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ViettelPost\Model\Config\Source;

class OrderService implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'VCN', 'label' => "VCN Chuyển phát nhanh"],
            ['value' => 'VTK', 'label' => "VTK Tiết kiệm"],
            ['value' => 'V60', 'label' => "V60 Dịch vụ Nhanh 60h"],
            ['value' => 'VVT', 'label' => "VVT Dịch vụ vận tải"],
            ['value' => 'VHT', 'label' => "VHT Phát Hỏa tốc"],
            ['value' => 'SCOD', 'label' => "SCOD Giao hàng thu tiền"],
            ['value' => 'PTN', 'label' => "PTN Phát trong ngày nội tỉnh"],
            ['value' => 'PHT', 'label' => "PHT Phát hỏa tốc nội tỉnh"],
            ['value' => 'PHS', 'label' => "PHS Phát hôm sau nội tỉnh"],
            ['value' => 'VBS', 'label' => "VBS Nhanh theo hộp"],
            ['value' => 'VBE', 'label' => "VBE Tiết kiệm theo hộp"],
        ];
    }
}
