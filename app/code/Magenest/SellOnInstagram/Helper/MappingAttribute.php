<?php


namespace Magenest\SellOnInstagram\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class MappingAttribute
 * @package Magenest\SellOnInstagram\Helper
 */
class MappingAttribute extends AbstractHelper
{
    /**
     * @return array[]
     */
    public function getFbShoppingAttribute()
    {
        return [
            [
                'label'      => 'Basic product data',
                'attributes' => [
                    [
                        'label'     => 'Product ID (fb:id)',
                        "name"      => "id",
                        "feed_name" => "fb:id",
                        "format"    => "required"
                    ],
                    [
                        'label'     => 'Product title (fb:name)',
                        "name"      => "name",
                        "feed_name" => "fb:name",
                        "format"    => "required"
                    ],
                    [
                        'label'     => 'Product description (fb:description)',
                        "name"      => "description",
                        "feed_name" => "fb:description",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Product URL (fb:url)',
                        "name"      => "url",
                        "feed_name" => "fb:url",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Main image URL (fb:image_url)',
                        "name"      => "image_url",
                        "feed_name" => "fb:image_url",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Additional image URL (fb:additional_image_urls)',
                        "name"      => "additional_image_urls",
                        "feed_name" => "fb:additional_image_urls",
                        "format"    => "optional",
                    ],
                ],
            ],
            [
                'label'      => 'Price & Availability',
                'attributes' => [
                    [
                        'label'     => 'Stock status (fb:availability)',
                        "name"      => "availability",
                        "feed_name" => "fb:availability",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Price (fb:price)',
                        "name"      => "price",
                        "feed_name" => "fb:price",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Sale price (fb:sale_price)',
                        "name"      => "sale_price",
                        "feed_name" => "fb:sale_price",
                        "format"    => "optional",
                    ],
                ]
            ],
            [
                'label'      => 'Product category',
                'attributes' => [
                    [
                        'label'     => 'Google product category (fb:google_product_category)',
                        "name"      => "google_product_category",
                        "feed_name" => "fb:google_product_category",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Facebook product category (fb:fb_product_category)',
                        "name"      => "fb_product_category",
                        "feed_name" => "fb:fb_product_category",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Product type (fb:product_type)',
                        "name"      => "product_type",
                        "feed_name" => "fb:product_type",
                        "format"    => "optional",
                    ]
                ],
            ],
            [
                'label'      => 'Detailed product description',
                'attributes' => [
                    [
                        'label'     => 'Condition (fb:condition)',
                        "name"      => "condition",
                        "feed_name" => "fb:condition",
                        "format"    => "required",
                    ],
                    [
                        'label'     => 'Age group (fb:age_group)',
                        "name"      => "age_group",
                        "feed_name" => "fb:age_group",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Color (fb:color)',
                        "name"      => "color",
                        "feed_name" => "fb:color",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Brand (fb:brand)',
                        "name"      => "brand",
                        "feed_name" => "fb:brand",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Gender (fb:gender)',
                        "name"      => "gender",
                        "feed_name" => "fb:gender",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Pattern (fb:pattern)',
                        "name"      => "pattern",
                        "feed_name" => "fb:pattern",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Size (fb:size)',
                        "name"      => "size",
                        "feed_name" => "fb:size",
                        "format"    => "optional",
                    ],
                    [
                        'label'     => 'Item group ID (fb:item_group_id)',
                        "name"      => "item_group_id",
                        "feed_name" => "fb:item_group_id",
                        "format"    => "optional",
                    ],
                ]
            ],
        ];
    }
}
