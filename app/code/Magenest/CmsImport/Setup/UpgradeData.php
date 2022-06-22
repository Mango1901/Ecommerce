<?php

namespace Magenest\CmsImport\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    const YOUR_STORE_ID = 1;

    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    private $_blockFactory;

    /**
     * UpgradeData constructor
     *
     * @param \Magento\Cms\Model\BlockFactory $blockFactory
     */
    public function __construct(
        \Magento\Cms\Model\BlockFactory $blockFactory
    )
    {
        $this->_blockFactory = $blockFactory;
    }

    /**
     * Upgrade data for the module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Exception
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.2') < 0) {
            $cmsFooter1Block = $this->_blockFactory->create()->setStoreId(self::YOUR_STORE_ID)->load('footer-column1', 'identifier');

            $cmsFooter1BlockData = [
                'title' => 'Footer Column 1 VN',
                'identifier' => 'footer-column1',
                'is_active' => 1,
                'stores' => [self::YOUR_STORE_ID],
                'content' => "
                    <div class=\"col-label h8-le-des-gd-1\">Về chúng tôi</div>
                    <div class=\"col-content\">
                    <ul>
                    <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='about-us'}}\">Giới thiệu</a></li>
                    <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='maplist'}}\">Vị trí cửa hàng</a></li>
                    <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='faqs'}}\">Câu hỏi thường gặp</a></li>
                    <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='privacy-policy'}}\">Chính sách và điều khoản</a></li>
                    <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='b2b-customer-create.html'}}\">Đăng ký trở thành đối tác</a></li>
                    <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='contact/index'}}\">Liên hệ</a></li>
                    </ul>
                    <div class=\"social-list\">
                                    <a target=\"_blank\" href=\"http://www.facebook.com/cafe.net.vn\" class=\"social\"><span class=\"bg-facebook\"></span></a>
                                    <a href=\"\" class=\"social\"><span class=\"bg-instagram\"></span></a>
                                    <a href=\"\" class=\"social\"><span class=\"bg-youtube3\"></span></a>
                                    <a href=\"\" class=\"social\"><span class=\"bg-zalo\"></span></a>
                                </div>
                    </div>
                ",
            ];

            if (!$cmsFooter1Block->getId()) {
                $this->_blockFactory->create()->setData($cmsFooter1BlockData)->save();
            } else {
                $cmsFooter1Block->setContent($cmsFooter1BlockData['content'])->save();
            }

            //Footer Column 2
            $cmsFooter2Block = $this->_blockFactory->create()->setStoreId(self::YOUR_STORE_ID)->load('footer-column2', 'identifier');

            $cmsFooter2BlockData = [
                'title' => 'Footer Column 2 VN',
                'identifier' => 'footer-column2',
                'is_active' => 1,
                'stores' => [self::YOUR_STORE_ID],
                'content' => "
                    <div class=\"col-label h8-le-des-gd-1\">Đối tác của chúng tôi</div>
                    <div class=\"col-content\">
                        <div class=\"partner-hangmay\">
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-01.jpg'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-02.jpg'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-03.jpg'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-04.jpg'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-05.jpg'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-06.jpg'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-07.jpg'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/partners/partner-3-08.jpg'}}\"></div>
                        </div>
                        <a target=\"_blank\" href=\"http://www.online.gov.vn/HomePage/CustomWebsiteDisplay.aspx?DocId=5364\"><img class=\"logo-bct\" src=\"{{view url='images/logo-bct.png'}}\"></a>
                    </div>
                ",
            ];

            if (!$cmsFooter2Block->getId()) {
                $this->_blockFactory->create()->setData($cmsFooter2BlockData)->save();
            } else {
                $cmsFooter2Block->setContent($cmsFooter2BlockData['content'])->save();
            }


            //Footer Column 3
            $cmsFooter3Block = $this->_blockFactory->create()->setStoreId(self::YOUR_STORE_ID)->load('footer-column3', 'identifier');

            $cmsFooter3BlockData = [
                'title' => 'Footer Column 3 VN',
                'identifier' => 'footer-column3',
                'is_active' => 1,
                'stores' => [self::YOUR_STORE_ID],
                'content' => "
                    <div class=\"col-label h8-le-des-gd-1\">Đối tác thanh toán</div>
                    <div class=\"col-content\">
                        <div class=\"list-payment\">
                            <div class=\"item\"><img src=\"{{view url='images/payments/logo-payment-visa.png'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/payments/logo-payment-master.png'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/payments/logo-payment-jcb.png'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/payments/logo-payment-atm.png'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/payments/logo-payment-cod.png'}}\"></div>
                        </div>
                        <div class=\"partner-tragop\">
                            <div class=\"item\"><img src=\"{{view url='images/payments/alepay.png'}}\"></div>
                            <div class=\"item\"><img src=\"{{view url='images/payments/payoo.png'}}\"></div>
                        </div>
                        <a class=\"tragop-link text-xs-le-des-wh\" href=\"{{store url='thanh-toan-an-toan'}}\">Cách thức thanh toán</a>
                    </div>
                ",
            ];

            if (!$cmsFooter3Block->getId()) {
                $this->_blockFactory->create()->setData($cmsFooter3BlockData)->save();
            } else {
                $cmsFooter3Block->setContent($cmsFooter3BlockData['content'])->save();
            }


            //Footer Column 4
            $cmsFooter4Block = $this->_blockFactory->create()->setStoreId(self::YOUR_STORE_ID)->load('footer-column4', 'identifier');

            $cmsFooter4BlockData = [
                'title' => 'Footer Column 4 VN',
                'identifier' => 'footer-column4',
                'is_active' => 1,
                'stores' => [self::YOUR_STORE_ID],
                'content' => "
                    <div class=\"col-label h8-le-des-gd-1\">Đối tác vận chuyển</div>
                    <div class=\"col-content\">
                        <div class=\"partner-vanchuyen\">
                            <div class=\"item\"><img src=\"{{view url='images/ships/viettelpost.png'}}\" /></div>
                            <div class=\"item\"><img src=\"{{view url='images/ships/ship-60.png'}}\" /></div>
                        </div>
                        <ul>
                            <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='chinh-sach-bao-hanh'}}\">Chính sách bảo hành</a></li>
                            <li><a class=\"text-xs-le-des-wh\" href=\"{{store url='doi-hang'}}\">Chính sách đổi trả</a></li>
                        </ul>
                        <div class=\"text-xs-le-des-wh\">
                    <strong>Văn phòng</strong>: 82-84 Bùi Thị Xuân, P.Bến Thành, Q.1, TP.HCM.
                    <strong>Showroom</strong>: 324-326 Nguyễn Đình Chiểu, P.4, Q.3, TP.HCM. Đơn vị chủ quản: Công ty Cổ phần Tập Đoàn Trung Nguyên. Số ĐKKD: 0304324655. Điện thoại: (84.8) 39251852 / Fax: (84.8) 39251848.
                    <strong>ĐƯỜNG DÂY NÓNG</strong>: 19006016
                       </div>
                        <div class=\"copy-right text-xxs-le-des-gr\">© 2019 Kangaroo Legend</div>
                    </div>
                ",
            ];

            if (!$cmsFooter4Block->getId()) {
                $this->_blockFactory->create()->setData($cmsFooter4BlockData)->save();
            } else {
                $cmsFooter4Block->setContent($cmsFooter4BlockData['content'])->save();
            }

            //Footer Column 4
            $cmsFooterLogoBlock = $this->_blockFactory->create()->setStoreId(0)->load('footer-logo', 'identifier');

            $cmsFooterLogoBlockData = [
                'title' => 'Footer Logo',
                'identifier' => 'footer-logo',
                'content' => "
                <p><a class=\"logo\" title=\"\" href=\"{{store url=''}}\" aria-label=\"store logo\"> <img title=\"\" src=\"{{view url='images/footer-logo.png'}}\" alt=\"\" width=\"165\" height=\"122\"> </a></p>
                ",
                'is_active' => 1,
                'stores' => [0],
                'sort_order' => 0
            ];

            if (!$cmsFooterLogoBlock->getId()) {
                $this->_blockFactory->create()->setData($cmsFooterLogoBlockData)->save();
            } else {
                $cmsFooterLogoBlock->setContent($cmsFooterLogoBlockData['content'])->save();
            }
        }

        $setup->endSetup();
    }
}
