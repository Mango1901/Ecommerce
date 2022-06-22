<?php
/**
 * Copyright © Magenest JSC. All rights reserved.
 *
 * Created by PhpStorm.
 * User: crist
 * Date: 08/11/2021
 * Time: 16:42
 */

namespace Magenest\Affiliate\Model\Account;

class BankInfo implements \Magento\Framework\Data\OptionSourceInterface
{
    const OPTIONS = [
        970423 => [
            'value' => 970423,
            'label' => 'TPBANK - Ngân hàng TMCP Tiên Phong',
            'type' => [0, 1]
        ],
        970437 => [
            'value' => 970437,
            'label' => 'HDBANK - Ngân hàng TMCP Phát Triển Thành Phố Hồ Chí Minh',
            'type' => [0, 1]
        ],
        970408 => [
            'value' => 970408,
            'label' => 'GPBANK - Ngân hàng TM TNHH MTV Dầu Khí Toàn Cầu',
            'type' => [0, 1]
        ],
        970407 => [
            'value' => 970407,
            'label' => 'TECHCOMBANK - Ngân hàng TMCP Kỹ thương Việt Nam',
            'type' => [0, 1]
        ],
        970442 => [
            'value' => 970442,
            'label' => 'HLBVN - Ngân hàng TNHH MTV Hong Leong Việt Nam',
            'type' => [0, 1]
        ],
        970414 => [
            'value' => 970414,
            'label' => 'OCEANBANK - Ngân hàng TMCP Đại Dương',
            'type' => [0, 1]
        ],
        970438 => [
            'value' => 970438,
            'label' => 'BAOVIETBANK - Ngân hàng TMCP Bảo Việt',
            'type' => [0, 1]
        ],
        970422 => [
            'value' => 970422,
            'label' => 'MBBANK - Ngân hàng TMCP Quân Đội',
            'type' => [0, 1]
        ],
        970432 => [
            'value' => 970432,
            'label' => 'VPBANK - Ngân hàng TMCP Việt Nam Thịnh Vương',
            'type' => [0, 1]
        ],
        970439 => [
            'value' => 970439,
            'label' => 'PUBLICBANK - Ngân hàng TNHH MTV Public Việt Nam',
            'type' => [0, 1]
        ],
        970415 => [
            'value' => 970415,
            'label' => 'VIETINBANK - Ngân hàng TMCP Công Thương Việt Nam',
            'type' => [0, 1]
        ],
        970431 => [
            'value' => 970431,
            'label' => 'EXIMBANK - Ngân hàng TMCP Xuất nhập khẩu Việt Nam',
            'type' => [0, 1]
        ],
        970440 => [
            'value' => 970440,
            'label' => 'SEABANK - Ngân hàng TMCP Đông Nam Á',
            'type' => [0, 1]
        ],
        970429 => [
            'value' => 970429,
            'label' => 'SCB - Ngân hàng TMCP Sài Gòn',
            'type' => [0, 1]
        ],
        970448 => [
            'value' => 970448,
            'label' => 'OCB - Ngân hàng TMCP Phương Đông',
            'type' => [0, 1]
        ],
        970425 => [
            'value' => 970425,
            'label' => 'ABBANK - Ngân hàng TMCP An Bình',
            'type' => [0, 1]
        ],
        970426 => [
            'value' => 970426,
            'label' => 'MSB - Ngân hàng TMCP Hàng Hải Việt Nam',
            'type' => [0, 1]
        ],
        970427 => [
            'value' => 970427,
            'label' => 'VIETABANK - Ngân hàng TMCP Việt Á',
            'type' => [0, 1]
        ],
        970419 => [
            'value' => 970419,
            'label' => 'NCB - Ngân hàng TMCP Quốc Dân',
            'type' => [0, 1]
        ],
        970418 => [
            'value' => 970418,
            'label' => 'BIDV - Ngân hàng TMCP Đầu tư và Phát triển Việt Nam',
            'type' => [0, 1]
        ],
        970443 => [
            'value' => 970443,
            'label' => 'TMCP - Ngân hàng TMCP Sài Gòn - Hà Nội',
            'type' => [0, 1]
        ],
        970406 => [
            'value' => 970406,
            'label' => 'DONGA - Ngân hàng TMCP Đông Á',
            'type' => [0, 1]
        ],
        970441 => [
            'value' => 970441,
            'label' => 'VIB - Ngân hàng TMCP Quốc Tế',
            'type' => [0, 1]
        ],
        970424 => [
            'value' => 970424,
            'label' => 'SHINHAN - Ngân hàng TNHH MTV Shinhan Việt Nam',
            'type' => [0, 1]
        ],
        970433 => [
            'value' => 970433,
            'label' => 'VIETBANK - Ngân hàng TMCP Việt Nam Thương Tín',
            'type' => [0, 1]
        ],
        970454 => [
            'value' => 970454,
            'label' => 'VIETCAPITALBANK - Ngân hàng TMCP Bản Việt',
            'type' => [0]
        ],
        970452 => [
            'value' => 970452,
            'label' => 'KIENLONGBANK - Ngân hàng TMCP Kiên Long',
            'type' => [0, 1]
        ],
        970430 => [
            'value' => 970430,
            'label' => 'PGBANK - Ngân hàng TMCP Xăng Dầu Petrolimex',
            'type' => [0, 1]
        ],
        970400 => [
            'value' => 970400,
            'label' => 'SAIGONBANK - Ngân hàng TMCP Sài Gòn Công Thương',
            'type' => [0, 1]
        ],
        970405 => [
            'value' => 970405,
            'label' => 'AGRIBANK - Ngân hàng Nông Nghiệp và Phát triển Nông Thôn Việt Nam',
            'type' => [0, 1]
        ],
        970403 => [
            'value' => 970403,
            'label' => 'SACOMBANK - Ngân hàng TMCP Sài Gòn Thương Tín',
            'type' => [0, 1]
        ],
        970412 => [
            'value' => 970412,
            'label' => 'PVCOMBANK - Ngân hàng TMCP Đại Chúng Việt Nam',
            'type' => [0, 1]
        ],
        970421 => [
            'value' => 970421,
            'label' => 'VRB - Ngân hàng Liên Doanh Việt Nga',
            'type' => [0, 1]
        ],
        970428 => [
            'value' => 970428,
            'label' => 'NAMABANK - Ngân hàng TMCP Nam Á',
            'type' => [0, 1]
        ],
        970434 => [
            'value' => 970434,
            'label' => 'IVB - Ngân hàng TNHH Indovina',
            'type' => [0, 1]
        ],
        970449 => [
            'value' => 970449,
            'label' => 'LIENVIETPOSTBANK - Ngân hàng TMCP Bưu Điện Liên Việt',
            'type' => [0, 1]
        ],
        970457 => [
            'value' => 970457,
            'label' => 'WOORIBANK - Ngân hàng Wooribank',
            'type' => [0]
        ],
        970436 => [
            'value' => 970436,
            'label' => 'VIETCOMBANK - Ngân hàng TMCP Ngoại thương Việt Nam',
            'type' => [0, 1]
        ],
        970416 => [
            'value' => 970416,
            'label' => 'ACB - Ngân hàng TMCP Á Châu',
            'type' => [0]
        ],
        970409 => [
            'value' => 970409,
            'label' => 'BACABANK - Ngân hàng TMCP Bắc Á',
            'type' => [1]
        ],
        970458 => [
            'value' => 970458,
            'label' => 'UOB - Ngân hàng TNHH MTV United Overseas Bank',
            'type' => [0, 1]
        ],
        970446 => [
            'value' => 970446,
            'label' => 'COOPBANK - Ngân hàng hợp tác CoopBank',
            'type' => [1]
        ],
        422589 => [
            'value' => 422589,
            'label' => 'CIMB - Ngân hàng TNHH MTV CIMB Việt Nam',
            'type' => [0, 1]
        ],
        970455 => [
            'value' => 970455,
            'label' => 'SGBANK - Ngân hàng Công nghiệp Hàn Quốc – Chi nhánh Hà Nội',
            'type' => [0]
        ],
        970444 => [
            'value' => 970444,
            'label' => 'CBBANK - Ngân hàng TM TNHH MTV Xây Dựng Việt Nam',
            'type' => [0]
        ],
        796500 => [
            'value' => 796500,
            'label' => 'DBS - Ngân hàng DBS - chi nhánh Thành phố Hồ Chí Minh',
            'type' => [0]
        ],
        458761 => [
            'value' => 458761,
            'label' => 'HSBC - Ngân hàng TNHH MTV HSBC (Việt Nam)',
            'type' => [0]
        ],
        970456 => [
            'value' => 970456,
            'label' => 'IBK - Ngân hàng IBK - chi nhánh HCM',
            'type' => [0]
        ],
        970462 => [
            'value' => 970462,
            'label' => 'Kookmin - Ngân hàng Kookmin - Chi nhánh Hà Nội',
            'type' => [0]
        ],
        801011 => [
            'value' => 801011,
            'label' => 'NONGHYUP - Ngân hàng NONGHYUP - Chi nhánh Hà Nội',
            'type' => [0]
        ],
        970410 => [
            'value' => 970410,
            'label' => 'STANDARDCHARTERED - Ngân hàng TNHH MTV Standard Chartered Việt Nam',
            'type' => [0]
        ],
        546034 => [
            'value' => 546034,
            'label' => 'CAKE - Ngân hàng số CAKE by VPBank',
            'type' => [0]
        ],
        546035 => [
            'value' => 546035,
            'label' => 'UBANK - Ngân hàng số Ubank by VPBank',
            'type' => [0]
        ]
    ];

    public function getRawOptions()
    {
        return self::OPTIONS;
    }

    public function toOptionArray()
    {
        $options = self::OPTIONS;
        usort($options, function ($first, $second) {
            return ($first['label'] <= $second['label']) ? -1 : 1;
        });

        array_unshift($options, [
            'value' => "",
            'label' => __('Please choose your bank'),
            'type' => [0, 1]
        ]);

        return $options;
    }
}
