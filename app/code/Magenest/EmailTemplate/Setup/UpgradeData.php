<?php
/**
 * Copyright © 2019 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_tn233 extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package Magenest_Kangaroo
 */

namespace Magenest\EmailTemplate\Setup;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Email\Model\TemplateFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    protected $emailTemplate = [
        'VN-Email Chào mừng CTV',
        'VN-Email Chào Mừng KH mới',
        'VN-Email Giao Hàng Thành Công',
        'VN-Email Quên Mật Khẩu',
        'VN-Email Thông Báo Bảo Hành Xong',
        'VN-Email Xác nhận TK B2C',
        'VN-Email Xác Nhận Đơn Hàng',
        'VN-Email Đang Giao Hàng'
    ];
    protected $enEmailTemplate = [
        'EN-Email Welcome partner',
        'EN-Email Welcome new customer',
        'EN-Email Successful delivery',
        'EN-Email Password forgotten',
        'EN-Email Warranty completion notice',
        'EN-Email B2C account confirmation',
        'EN-Email Order confirmation',
        'EN-Email On delivery'
    ];
    /**
     * Template factory
     *
     * @var TemplateFactory
     */
    private $templateFactory;

    /**
     * Init
     *
     * @param TemplateFactory $templateFactory
     */
    public function __construct(TemplateFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            $this->templateFactory->create()->getCollection()
                ->addFieldToFilter('template_code', ['in' => $this->emailTemplate])
                ->walk('delete');
            $this->createEmailChaoMungCTV();
            $this->createEmailChaoMungKHMoi();
            $this->createEmailQuenMatKhau();
            $this->createEmailThongBaoBaoHanhXong();
            $this->createEmailXacNhanTKB2C();
            $this->createEmailDangGiaoHang();
            $this->createEmailGiaoHangThanhCong();
            $this->createEmailXacNhanDonHang();
        }
        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $this->templateFactory->create()->getCollection()
                ->addFieldToFilter('template_code', ['in' => $this->enEmailTemplate])
                ->walk('delete');
            $this->createEmailWelcomePartner();
            $this->createEmailWelcomeNewCustomer();
            $this->createEmailSuccessfulDelivery();
            $this->createEmailPasswordForgotten();
            $this->createEmailWarrantyCompletionNotice();
            $this->createEmailB2CAccountConfirmation();
            $this->createEmailOrderConfirmation();
            $this->createEmailOnDelivery();
        }
    }

    private function createEmailChaoMungCTV()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Chào mừng CTV');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;\'><strong>Xin chào bạn {{trans "%name" name=$customer.name}}</strong>, <br/></p>
							<p style=\'line-height:22px;\'><strong>Cảm ơn bạn đã đăng ký trở thành CTV tại Coffee Hypermarket – CAFE.NET.VN </strong><br/></p>

							<p style=\'line-height:22px;\'>Hướng dẫn tạo link chia sẻ sản phẩm tại <a href=\'{{store url=\'amasty_affiliate/account/promo/\'}}\' style=\'color:#1E7EC8;\'>đây</a></p>

							<p style=\'line-height:22px;\'>Chúng tôi sẽ tạo điều kiện tối đa để cho các bạn CTV có thể yên tâm hoạt động phát triển lâu dài. Đối tác tham gia hợp tác kinh doanh nhằm tăng giá trị cho cả hai và cùng nhau phát triển bền vững.</p>							<p style="line-height:22px;">Là thành viên của CAFE.NET.VN, quý khách sẽ là người đầu tiên nhận được tin tức về các chương trình khuyến mãi độc quyền và giảm giá đặc biệt tại CAFE.NET.VN. Ngoài ra, những thông tin về sản phẩm và dịch vụ mới nhất sẽ luôn được cập nhật đến quý khách.</p>
							<p style="line-height:22px;">Chúc quý khách một ngày vui vẻ!</p>
						</td>
					</tr>
					<tr>
						<td>
							<p style=\'font-size:12px; margin:0;\'>Trân trọng,<br/>Trung Tâm Dịch vụ Khách hàng </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style=\'margin-top: 20px;\'>
						<td valign=\'bottom\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/footer/footer.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Xác nhận đăng ký Tài khoản CTV thành công');
        $template->setOrigTemplateCode('');
        $template->save();
    }

    private function createEmailChaoMungKHMoi()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Chào Mừng KH mới');
        $template->setTemplateText('<div style="background:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
	<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
		<tr>
			<td align="center" valign="top" style="padding:20px 0 20px 0">
				<table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="582">
					<!-- [ header starts here] -->
					<tr style="margin-bottom:10px;">
						<td valign="top" style="text-align: center;">
							<a href="{{store url=""}}"><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign="top">
							<h1 style="font-size:14px; font-weight:normal; margin:0 0 11px 0;">Kính chào quý khách {{trans "%name" name=$customer.name}},</h1>
							<p style="line-height:22px;">
								Chào mừng quý khách đến với <a href="{{store url=""}}" style="color:#1E7EC8;">CAFE.NET.VN</a> - Thế Giới Cà Phê – Cà Phê Thế Giới! Là thành viên của CAFE.NET.VN, quý khách sẽ là người đầu tiên nhận được những thông tin về các chương trình khuyến mại đặc sắc dành riêng cho bạn và các chương trình giảm giá đặc biệt tại <a href="{{store url=""}}" style="color:#1E7EC8;">CAFE.NET.VN</a>
							</p>
                                Email và mật khẩu dùng để đăng nhập:<br/>
                                <strong>E-mail:</strong>{{trans "%email" email=$customer.email}}<br/>
                                <strong>Password:</strong>{{trans "%password" password=$customer.password}}<p>
							<p style="line-height:22px;">Chúng tôi hy vọng quý khách sẽ có những trải nghiệm mua sắm thú vị khi đến với <a href="{{store url=""}}" style="color:#1E7EC8;">CAFE.NET.VN</a>. Khi cần hỗ trợ vui lòng liên hệ <b>1900 6016</b> hoặc gửi thư điện tử về địa chỉ: <a href="mailto:{{config path=\'trans_email/ident_support/email\'}}" style="color:#1E7EC8;">{{config path=\'trans_email/ident_support/email\'}}</a>. </p>
							<p style="line-height:22px;">Chúc bạn một ngày vui vẻ!!</p>
						</td>
					</tr>
					<tr>
						<td>
							<p style="line-height:20px;font-size:12px; margin:0;">Trân trọng,<br/>Trung Tâm Dịch vụ Khách hàng </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style="margin-top: 20px;">
						<td valign="bottom" style="text-align: center;">
							<a href="{{store url=""}}"><img src="{{media url="email/footer/footer.png" }}" alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateSubject('CAFE.NET.VN - Xác nhận đăng ký Tài khoản thành công');
        $template->setOrigTemplateCode('customer_create_account_email_confirmed_template');
        $template->setTemplateType(2);
        $template->setOrigTemplateVariables('{"store url=\"\"":"Store Url",
"var logo_url":"Email Logo Image Url",
"htmlescape var=$customer.name":"Customer Name",
"store url=\"customer/account/\"":"Customer Account Url",
"var customer.email":"Customer Email",
"htmlescape var=$customer.password":"Customer Password"}');
        $template->save();
    }

    private function createEmailQuenMatKhau()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Quên Mật Khẩu');
        $template->setTemplateText('<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;">
	<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
		<tr>
			<td align="center" valign="top" style="padding: 20px 0 20px 0">
				<table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="582">
					<tr>
						<td valign="top" style="text-align: center;">
							<a href="{{store url=""}}" style="color:#1E7EC8;"><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Xin chào quý khách {{trans "%name" name=$customer.name}}</h1>
							<p style="line-height:22px;font-size: 12px; margin: 0 0 8px 0;">Theo yêu cầu của quý khách, mật khẩu đã được thiết lập lại. Xin vui lòng bấm vào <a href=\'{{var this.getUrl($store,\'customer/account/createPassword/\',[_query:[token:$customer.rp_token],_nosid:1])}}\' target=\'_blank\'>đây</a> để lấy lại mật khẩu.</p>
							<p style="line-height:22px;font-size: 12px; margin: 0;">Chúng tôi hy vọng quý khách sẽ có những trải nghiệm mua sắm thú vị khi đến với CAFE.NET.VN. Nếu có câu hỏi cần giải đáp, quý khách vui lòng liên hệ số điện thoại 1900 6016 hoặc gửi thư điện tử về địa chỉ: <a href="mailto:{{config path=\'trans_email/ident_support/email\'}}" style="color:#1E7EC8;">{{config path=\'trans_email/ident_support/email\'}}</a>.</p>
							<p style="line-height:22px;font-size: 12px; margin: 0;">Chúc quý khách một ngày vui vẻ!</p>
						</td>
					</tr>
					<tr>
						<td>
							<p style="font-size:12px; margin:0;">Trân trọng,<br/>Trung Tâm Dịch vụ Khách hàng </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style="margin-top: 20px;">
						<td valign="bottom" style="text-align: center;">
							<a href="{{store url=""}}"><img src="{{media url="email/footer/footer.png" }}" alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateSubject('CAFE.NET.VN - Phục hồi mật khẩu');
        $template->setTemplateType(2);
        $template->setOrigTemplateCode('customer_password_forgot_email_template');
        $template->setOrigTemplateVariables('{"store url=\"\"":"Store Url",
"var logo_url":"Email Logo Image Url",
"var logo_alt":"Email Logo Image Alt",
"htmlescape var=$customer.name":"Customer Name",
"store url=\"customer/account/resetpassword/\" _query_id=$customer.id _query_token=$customer.rp_token":"Reset Password URL"}');
        $template->save();
    }

    private function createEmailThongBaoBaoHanhXong()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Thông Báo Bảo Hành Xong');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;\'><strong>Kính chào quý khách {{trans "%name" name=$customer.name}}</strong>, <br/></p>
							<p style=\'line-height:22px;\'><strong>Yêu cầu bảo hành/sửa chữa sản phẩm của quý khách đã được thực hiện xong. Trung Tâm Bảo Hành sẽ liên hệ với quý khách cách thức giao nhận sản phẩm. </strong><br/></p>


						</td>
					</tr>
					<tr>
						<td>
							<p style=\'font-size:12px; margin:0;\'>Trân trọng,<br/>Trung Tâm Bảo Hành. </p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Thông báo bảo hành hoàn thành');
        $template->setOrigTemplateCode('');
        $template->save();
    }

    private function createEmailXacNhanTKB2C()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Xác nhận TK B2C');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;\'><strong>Kính chào quý khách {{trans "%name" name=$customer.name}}</strong>, <br/></p>
							<p style=\'line-height:22px;\'><strong>Cảm ơn quý khách đã đăng ký tài khoản tại Coffee Hypermarket – CAFE.NET.VN</strong><br/></p>

							<p style="line-height:22px;">Quý khách vui lòng click vào  <a href=\'{{var this.getUrl($store,\'customer/account/confirm/\',[_query:[id:$customer.id,key:$customer.confirmation,back_url:$back_url],_nosid:1])}}\' target=\'_blank\'>đây</a> để kích hoạt tài khoản tại <strong>{{var logo_alt}}</strong></p>
							<p style="line-height:22px;">Nếu link trên không hoat động quý khách có thể sao chép đường dẫn dưới và dán vào trình duyệt</p>
							<p style="line-height:22px;"><b><i>this.getUrl($store,\'customer/account/confirm/\',[_query:[id:$customer.id,key:$customer.confirmation,back_url:$back_url],_nosid:1]</i></b></p>

							<p style="line-height:22px;"><strong>Coffee Hypermarket - CAFE.NET.VN </strong> là kênh phân phối online của Công ty Cổ phần Tập đoàn Trung Nguyên<br/></p>
							<p style="line-height:22px;">Đây là siêu thị cà phê online đầu tiên và duy nhất tại Việt Nam - Với chỉ một "click" cả thế giới cà phê mở ra trước mắt bạn<br/></p>
						</td>
					</tr>
					<tr>
						<td>
							<p style=\'font-size:12px; margin:0;\'>Trân trọng,<br/>Trung Tâm Dịch vụ Khách hàng </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style=\'margin-top: 20px;\'>
						<td valign=\'bottom\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/footer/footer.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Xác nhận tài khoản dành cho {{var customer.name}}');
        $template->setOrigTemplateCode('customer_create_account_email_confirmation_template');
        $template->setOrigTemplateVariables('{"store url=\"\"":"Store Url",
"skin url=\"images/logo_email.gif\" _area=\'frontend\'":"Email Logo Image",
"store url=\"customer/account/\"":"Customer Account Url",
"htmlescape var=$customer.name":"Customer Name",
"var customer.email":"Customer Email",
"store url=\"customer/account/confirm/\" _query_id=$customer.id _query_key=$customer.confirmation _query_back_url=$back_url":"Confirmation Url",
"htmlescape var=$customer.password":"Customer password"}');
        $template->save();
    }

    private function createEmailDangGiaoHang()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Đang Giao Hàng');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;font-size:16px\'>Một kiện hàng đã được giao cho đơn vị vận chuyển</p>
							<img style=\'display: block;margin-left: auto;margin-right: auto;max-width:300px\' src=\'{{media url=\'email/shipment/delivery.jpg\' }}\' alt=\'delivery\' border=\'0\'/>

							<p style=\'line-height:22px;font-size:16px\'>{{trans "%customer_name," customer_name=$order.getCustomerName()}} thân mến,</p>
							<p style=\'line-height:22px;\'>Một kiện hàng thuộc đơn hàng {{trans \'<span class="no-link">#%increment_id</span>\' increment_id=$order.increment_id |raw}} đã được đóng gói và giao cho đơn vị vận chuyển.</p>
							<p style=\'line-height:22px;\'>Trước khi giao hàng, đơn vị vận chuyển sẽ liên hệ với bạn để hẹn ngày và thời gian giao hàng nhé!</p>
							<p style=\'line-height:22px;font-size:14px\'>Bước tiếp theo</p>
							<ul>
							<li style=\'line-height:22px;\'>Kiểm tra kỹ thông tin đơn hàng trước khi thanh toán, kiểm tra ngoại quan kiện hàng (thông tin mua hàng, tình trạng kiện hàng, ..), kiểm tra số lượng và ngoại quan sản phẩm. Nếu bạn phát hiện kiện hàng có dấu hiệu móp méo, không còn nguyên vẹn, sản phẩm có dấu hiệu hư hỏng/ bể vỡ hoặc không đúng với thông tin trên website hoặc sai thông tin người nhận, vui lòng từ chối nhận hàng. Cafe.net.vn khuyến khích người mua nên chụp lại kiện hàng trước và sau khi nhận hàng để làm bằng chứng nếu có tranh chấp về sau.</li>
							<li style=\'line-height:22px;\'>Vui lòng giữ nguyên vẹn biên nhận bán hàng được dán trên thùng hàng, hóa đơn (nếu có) và hộp sản phẩm để đổi trả hàng hoặc bảo hành khi cần.</li>
                            </ul>
                            <p style=\'line-height:22px;font-size:14px\'>Đơn hàng được giao đến</p>
                             <p>{{var formattedShippingAddress|raw}}</p>
                             {{layout handle="sales_email_order_shipment_items" shipment=$shipment order=$order}}
                                 <p>Hình thức thanh toán</p>
                        {{var payment_html|raw}}
         <p>Tùy chọn vận chuyển</h3>
                        <p>{{var order.getShippingDescription()}}</p>
                        {{if shipping_msg}}
                        <p>{{var shipping_msg}}</p>
                        {{/if}}
                            <p style=\'line-height:22px;font-size:15px\'>HOTLINE: 19006016</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Đơn hàng đã được giao cho đơn vị vận chuyển');
        $template->setOrigTemplateCode('sales_email_shipment_template');
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var this.getUrl($store, \'customer/account/\')":"Customer Account URL","var order.getCustomerName()":"Customer Name","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var comment":"Shipment Comment","var shipment.increment_id":"Shipment Id","layout handle=\"sales_email_order_shipment_items\" shipment=$shipment order=$order":"Shipment Items Grid","block class=\'Magento\\\\Framework\\\\View\\\\Element\\\\Template\' area=\'frontend\' template=\'Magento_Sales::email\/shipment\/track.phtml\' shipment=$shipment order=$order":"Shipment Track Details","var formattedShippingAddress|raw":"Shipping Address","var order.shipping_description":"Shipping Description","var order.getShippingDescription()":"Shipping Description"}');
        $template->save();
    }

    private function createEmailGiaoHangThanhCong()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Giao Hàng Thành Công');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;font-size:16px\'>Giao hàng thành công</p>
							<img style=\'display: block;margin-left: auto;margin-right: auto;max-width:300px\' src=\'{{media url=\'email/shipment/success.jpg\' }}\' alt=\'delivery\' border=\'0\'/>

							<p style=\'line-height:22px;font-size:16px\'>{{trans "%customer_name," customer_name=$order.getCustomerName()}} thân mến,</p>
							<p style=\'line-height:22px;\'>Đơn hàng {{trans \'<span class="no-link">#%increment_id</span>\' increment_id=$order.increment_id |raw}} của bạn đã được giao đầy đủ với các sản phẩm được liệt kê bên dưới. Coffee Hypermarket – CAFE.NET.VN hi vọng bạn hài lòng với các sản phẩm này!</p>
						    {{layout handle="sales_email_order_shipment_items" shipment=$shipment order=$order}}
						        <p>Hình thức thanh toán</p>
                        {{var payment_html|raw}}
         <p>Tùy chọn vận chuyển</h3>
                        <p>{{var order.getShippingDescription()}}</p>
                        {{if shipping_msg}}
                        <p>{{var shipping_msg}}</p>
                        {{/if}}
                            <p style=\'line-height:22px;font-size:15px\'>Lưu ý</p>
                            <p style=\'line-height:22px;\'>Bạn vui lòng giữ lại hóa đơn, hộp sản phẩm và phiếu bảo hành (nếu có) để đổi trả hàng hoặc bảo hành khi cần thiết.</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Đơn hàng đã được giao thành công');
        $template->setOrigTemplateCode('sales_email_shipment_template');
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var this.getUrl($store, \'customer/account/\')":"Customer Account URL","var order.getCustomerName()":"Customer Name","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var comment":"Shipment Comment","var shipment.increment_id":"Shipment Id","layout handle=\"sales_email_order_shipment_items\" shipment=$shipment order=$order":"Shipment Items Grid","block class=\'Magento\\\\Framework\\\\View\\\\Element\\\\Template\' area=\'frontend\' template=\'Magento_Sales::email\/shipment\/track.phtml\' shipment=$shipment order=$order":"Shipment Track Details","var formattedShippingAddress|raw":"Shipping Address","var order.shipping_description":"Shipping Description","var order.getShippingDescription()":"Shipping Description"}');
        $template->save();
    }

    private function createEmailXacNhanDonHang()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('VN-Email Xác Nhận Đơn Hàng');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;font-size:16px\'><strong>Cảm ơn quý khách {{trans "%customer_name," customer_name=$order.getCustomerName()}} đã đặt hàng tại CAFE.NET.VN,</strong></p>
							<p style=\'line-height:22px;\'>CAFE.NET.VN rất vui thông báo đơn hàng {{trans \'<span class="no-link">#%increment_id</span>\' increment_id=$order.increment_id |raw}} của quý khách đã được tiếp nhận và đang trong quá trình xử lý. CAFE.NET.VN sẽ thông báo đến quý khách ngay khi hàng chuẩn bị được giao.</p>
							<p style=\'line-height:22px;\'>*Lưu ý nhỏ cho bạn: Để đảm bảo bạn sẽ nhận đúng hàng, hãy chỉ nhận hàng khi đơn hàng được cập nhật trạng thái là <strong>"Đang giao hàng"</strong> nhé!</p>
						    <p style=\'line-height:22px;font-size:14px\'>Đơn hàng được giao đến</p>
                             <p>{{var formattedShippingAddress|raw}}</p>
                               {{layout handle="sales_email_order_items" order=$order area="frontend"}}
                                 <p>Hình thức thanh toán</p>
                        {{var payment_html|raw}}
         <p>Tùy chọn vận chuyển</h3>
                        <p>{{var order.getShippingDescription()}}</p>
                        {{if shipping_msg}}
                        <p>{{var shipping_msg}}</p>
                        {{/if}}
                            <p style=\'line-height:22px;\'><strong>Bạn muốn biết chính xác khi nào nhận được hàng?</strong></p>
                            <p style=\'line-height:22px;\'>Trước khi giao hàng, đơn vị vận chuyển sẽ liên hệ với bạn để hẹn ngày và thời gian giao hàng nhé!</p>
                           <p style=\'line-height:22px;font-size:15px\'>HOTLINE: 19006016</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Xác nhận đơn hàng');
        $template->setOrigTemplateCode('sales_email_order_template');
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var order.getEmailCustomerNote()":"Email Order Note","var order.increment_id":"Order Id","layout handle=\"sales_email_order_items\" order=$order area=\"frontend\"":"Order Items Grid","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.getShippingDescription()":"Shipping Description","var shipping_msg":"Shipping message"}');
        $template->save();
    }

    private function createEmailWelcomePartner()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email Welcome partner');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;\'><strong>Greetings {{trans "%name" name=$customer.name}}</strong>, <br/></p>
							<p style=\'line-height:22px;\'><strong>Thank you for your partner registration on Coffee Hypermarket – CAFE.NET.VN </strong><br/></p>

							<p style=\'line-height:22px;\'>Guide to create a sharable product link <a href=\'{{store url=\'amasty_affiliate/account/promo/\'}}\' style=\'color:#1E7EC8;\'>here</a></p>

							<p style=\'line-height:22px;\'>Our partners will be provided with the most advantageous conditions for a long-term development. Partners participation benefits our business cooperation and together we build a sustainable expansion.</p>							<p style="line-height:22px;">As a partner of CAFE.NET.VN, You will be the first to receive news about exclusive promotions and special discounts on CAFE.NET.VN. In addition, the latest product and service information will always be updated to you.</p>
							<p style="line-height:22px;">Have a great day!</p>
						</td>
					</tr>
					<tr>
						<td>
							<p style=\'font-size:12px; margin:0;\'>Best regards,<br/>Customer service center </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style=\'margin-top: 20px;\'>
						<td valign=\'bottom\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/footer/footer.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Successful partner registration confirmation');
        $template->setOrigTemplateCode('');
        $template->save();
    }

    private function createEmailWelcomeNewCustomer()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email Welcome new customer');
        $template->setTemplateText('<div style="background:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:12px; margin:0; padding:0;">
	<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
		<tr>
			<td align="center" valign="top" style="padding:20px 0 20px 0">
				<table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="582">
					<!-- [ header starts here] -->
					<tr style="margin-bottom:10px;">
						<td valign="top" style="text-align: center;">
							<a href="{{store url=""}}"><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign="top">
							<h1 style="font-size:14px; font-weight:normal; margin:0 0 11px 0;">Dear {{trans "%name" name=$customer.name}},</h1>
							<p style="line-height:22px;">
								Welcome to <a href="{{store url=""}}" style="color:#1E7EC8;">CAFE.NET.VN</a> - Coffee world – World of coffee!, a member of CAFE.NET.VN. You, our dear customer, will be the first to receive notifications about exclusive deals and special promotions at <a href="{{store url=""}}" style="color:#1E7EC8;">CAFE.NET.VN</a>
							</p>
                                Email and password to sign in:<br/>
                                <strong>E-mail:</strong>{{trans "%email" email=$customer.email}}<br/>
                                <strong>Password:</strong>{{trans "%password" password=$customer.password}}<p>
							<p style="line-height:22px;">Your enjoyable shopping experience at <a href="{{store url=""}}" style="color:#1E7EC8;">CAFE.NET.VN</a> is our contentment. Contact us <b>1900 6016</b> or send us an email: <a href="mailto:{{config path=\'trans_email/ident_support/email\'}}" style="color:#1E7EC8;">{{config path=\'trans_email/ident_support/email\'}}</a>. </p>
							<p style="line-height:22px;">Have a great day!</p>
						</td>
					</tr>
					<tr>
						<td>
							<p style="line-height:20px;font-size:12px; margin:0;">Best regards,<br/>Customer service center </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style="margin-top: 20px;">
						<td valign="bottom" style="text-align: center;">
							<a href="{{store url=""}}"><img src="{{media url="email/footer/footer.png" }}" alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateSubject('CAFE.NET.VN - Successful account registration confirmation');
        $template->setOrigTemplateCode('customer_create_account_email_confirmed_template');
        $template->setTemplateType(2);
        $template->setOrigTemplateVariables('{"store url=\"\"":"Store Url",
"var logo_url":"Email Logo Image Url",
"htmlescape var=$customer.name":"Customer Name",
"store url=\"customer/account/\"":"Customer Account Url",
"var customer.email":"Customer Email",
"htmlescape var=$customer.password":"Customer Password"}');
        $template->save();
    }

    private function createEmailPasswordForgotten()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email Password forgotten');
        $template->setTemplateText('<div style="background: #F6F6F6; font-family: Verdana, Arial, Helvetica, sans-serif; font-size: 12px; margin: 0; padding: 0;">
	<table cellspacing="0" cellpadding="0" border="0" height="100%" width="100%">
		<tr>
			<td align="center" valign="top" style="padding: 20px 0 20px 0">
				<table bgcolor="FFFFFF" cellspacing="0" cellpadding="10" border="0" width="582">
					<tr>
						<td valign="top" style="text-align: center;">
							<a href="{{store url=""}}" style="color:#1E7EC8;"><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
					<tr>
						<td valign="top">
							<h1 style="font-size: 22px; font-weight: normal; line-height: 22px; margin: 0 0 11px 0;">Dear {{trans "%name" name=$customer.name}}</h1>
							<p style="line-height:22px;font-size: 12px; margin: 0 0 8px 0;">, your password has been reset successfully. Please click <a href=\'{{var this.getUrl($store,\'customer/account/createPassword/\',[_query:[token:$customer.rp_token],_nosid:1])}}\' target=\'_blank\'>here</a> to retrieve your password.</p>
							<p style="line-height:22px;font-size: 12px; margin: 0;">Your enjoyable shopping experience at CAFE.NET.VN is our contentment. If you have any question, please contact us 1900 6016 or send us an email: <a href="mailto:{{config path=\'trans_email/ident_support/email\'}}" style="color:#1E7EC8;">{{config path=\'trans_email/ident_support/email\'}}</a>.</p>
							<p style="line-height:22px;font-size: 12px; margin: 0;">Have a great day!</p>
						</td>
					</tr>
					<tr>
						<td>
							<p style="font-size:12px; margin:0;">Best regards,<br/>Customer service center </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style="margin-top: 20px;">
						<td valign="bottom" style="text-align: center;">
							<a href="{{store url=""}}"><img src="{{media url="email/footer/footer.png" }}" alt="{{var logo_alt}}" border="0"/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateSubject('CAFE.NET.VN - Password recovery');
        $template->setTemplateType(2);
        $template->setOrigTemplateCode('customer_password_forgot_email_template');
        $template->setOrigTemplateVariables('{"store url=\"\"":"Store Url",
"var logo_url":"Email Logo Image Url",
"var logo_alt":"Email Logo Image Alt",
"htmlescape var=$customer.name":"Customer Name",
"store url=\"customer/account/resetpassword/\" _query_id=$customer.id _query_token=$customer.rp_token":"Reset Password URL"}');
        $template->save();
    }

    private function createEmailWarrantyCompletionNotice()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email Warranty completion notice');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;\'><strong>Dear {{trans "%name" name=$customer.name}}</strong>, <br/></p>
							<p style=\'line-height:22px;\'><strong>Your product warranty / repair request has been completed.  Warranty Service Center will contact you to deliver the product. </strong><br/></p>


						</td>
					</tr>
					<tr>
						<td>
							<p style=\'font-size:12px; margin:0;\'>Best regards,<br/>Warranty Service Center. </p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Warranty completion notice');
        $template->setOrigTemplateCode('');
        $template->save();
    }

    private function createEmailB2CAccountConfirmation()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email B2C account confirmation');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;\'><strong>Dear {{trans "%name" name=$customer.name}}</strong>, <br/></p>
							<p style=\'line-height:22px;\'><strong>Thank you for your registration on Coffee Hypermarket – CAFE.NET.VN</strong><br/></p>

							<p style="line-height:22px;">Please click <a href=\'{{var this.getUrl($store,\'customer/account/confirm/\',[_query:[id:$customer.id,key:$customer.confirmation,back_url:$back_url],_nosid:1])}}\' target="_blank">here</a> to activate your account at <strong>{{var logo_alt}}</strong></p>
							<p style="line-height:22px;">If the link above does not work, you may copy the url below and paste it in your browser and go.</p>
							<p style="line-height:22px;"><b><i>{{var this.getUrl($store,\'customer/account/confirm/\',[_query:[id:$customer.id,key:$customer.confirmation,back_url:$back_url],_nosid:1])}}</i></b></p>

							<p style="line-height:22px;"><strong>Coffee Hypermarket - CAFE.NET.VN </strong> is an online distribution channel belonging to Kangaroo Group Joint Stock Company<br/></p>
							<p style="line-height:22px;">This is the first and only online coffee supermarket in Vietnam - Just a "click" to bring the whole coffee world to your home.<br/></p>
						</td>
					</tr>
					<tr>
						<td>
							<p style=\'font-size:12px; margin:0;\'>Best regards,<br/>Customer service center </p>
						</td>
					</tr>
					<!-- [ footer starts here] -->
					<tr style=\'margin-top: 20px;\'>
						<td valign=\'bottom\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/footer/footer.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Account registration confirmation for {{var customer.name}}');
        $template->setOrigTemplateCode('customer_create_account_email_confirmation_template');
        $template->setOrigTemplateVariables('{"store url=\"\"":"Store Url",
"skin url=\"images/logo_email.gif\" _area=\'frontend\'":"Email Logo Image",
"store url=\"customer/account/\"":"Customer Account Url",
"htmlescape var=$customer.name":"Customer Name",
"var customer.email":"Customer Email",
"store url=\"customer/account/confirm/\" _query_id=$customer.id _query_key=$customer.confirmation _query_back_url=$back_url":"Confirmation Url",
"htmlescape var=$customer.password":"Customer password"}');
        $template->save();
    }

    private function createEmailOnDelivery()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email On delivery');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;font-size:16px\'>A package has been handed to the shipping unit</p>
							<img style=\'display: block;margin-left: auto;margin-right: auto;max-width:300px\' src=\'{{media url=\'email/shipment/delivery.jpg\' }}\' alt=\'delivery\' border=\'0\'/>

							<p style=\'line-height:22px;font-size:16px\'>{{trans "%customer_name," customer_name=$order.getCustomerName()}} thân mến,</p>
							<p style=\'line-height:22px;\'>A package from order {{trans \'<span class="no-link">#%increment_id</span>\' increment_id=$order.increment_id |raw}} has been packed and handed to the shipping unit.</p>
							<p style=\'line-height:22px;\'>Before delivery, the shipping unit will contact you to schedule a delivery time!</p>
							<p style=\'line-height:22px;font-size:14px\'>Next step</p>
							<ul>
							<li style=\'line-height:22px;\'>Please carefully check the order information before payment, check the bonded goods (purchase information, package status, ..), check the quantity and bonded products as well. If you find that the package is distorted, no longer intact, the product shows signs of damage / breakage or is not true to the information on the website or the wrong recipient information, please refuse to receive the goods. Cafe.net.vn encourages buyers to take a picture of the package before and after receiving as the evidence if there is a dispute later.</li>
							<li style=\'line-height:22px;\'>Please keep the sales receipt posted on the box, invoice (if any) and product box intact in exchange for return or warranty as needed.</li>
                            </ul>
                            <p style=\'line-height:22px;font-size:14px\'>The order delivered to</p>
                             <p>{{var formattedShippingAddress|raw}}</p>
                             {{layout handle="sales_email_order_shipment_items" shipment=$shipment order=$order}}
                                 <p>Payment method</p>
                        {{var payment_html|raw}}
         <p>Shipping options</h3>
                        <p>{{var order.getShippingDescription()}}</p>
                        {{if shipping_msg}}
                        <p>{{var shipping_msg}}</p>
                        {{/if}}
                            <p style=\'line-height:22px;font-size:15px\'>HOTLINE: 19006016</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - The order has been delivered to the shipping unit');
        $template->setOrigTemplateCode('sales_email_shipment_template');
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var this.getUrl($store, \'customer/account/\')":"Customer Account URL","var order.getCustomerName()":"Customer Name","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var comment":"Shipment Comment","var shipment.increment_id":"Shipment Id","layout handle=\"sales_email_order_shipment_items\" shipment=$shipment order=$order":"Shipment Items Grid","block class=\'Magento\\\\Framework\\\\View\\\\Element\\\\Template\' area=\'frontend\' template=\'Magento_Sales::email\/shipment\/track.phtml\' shipment=$shipment order=$order":"Shipment Track Details","var formattedShippingAddress|raw":"Shipping Address","var order.shipping_description":"Shipping Description","var order.getShippingDescription()":"Shipping Description"}');
        $template->save();
    }

    private function createEmailSuccessfulDelivery()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email Successful delivery');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;font-size:16px\'>Successful delivery</p>
							<img style=\'display: block;margin-left: auto;margin-right: auto;max-width:300px\' src=\'{{media url=\'email/shipment/success.jpg\' }}\' alt=\'delivery\' border=\'0\'/>

							<p style=\'line-height:22px;font-size:16px\'>Dear {{trans "%customer_name," customer_name=$order.getCustomerName()}},</p>
							<p style=\'line-height:22px;\'>Your order {{trans \'<span class="no-link">#%increment_id</span>\' increment_id=$order.increment_id |raw}} has been delivered successfully with all of the products listed below. Coffee Hypermarket – CAFE.NET.VN hope you are satisfied with these products!</p>
						    {{layout handle="sales_email_order_shipment_items" shipment=$shipment order=$order}}
						        <p>Payment method</p>
                        {{var payment_html|raw}}
         <p>Shipping options</h3>
                        <p>{{var order.getShippingDescription()}}</p>
                        {{if shipping_msg}}
                        <p>{{var shipping_msg}}</p>
                        {{/if}}
                            <p style=\'line-height:22px;font-size:15px\'>Notice</p>
                            <p style=\'line-height:22px;\'>Please retain the invoice, product box and warranty card (if any) for return or warranty as needed.</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Your order has been delivered successfully');
        $template->setOrigTemplateCode('sales_email_shipment_template');
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var this.getUrl($store, \'customer/account/\')":"Customer Account URL","var order.getCustomerName()":"Customer Name","var order.increment_id":"Order Id","var payment_html|raw":"Payment Details","var comment":"Shipment Comment","var shipment.increment_id":"Shipment Id","layout handle=\"sales_email_order_shipment_items\" shipment=$shipment order=$order":"Shipment Items Grid","block class=\'Magento\\\\Framework\\\\View\\\\Element\\\\Template\' area=\'frontend\' template=\'Magento_Sales::email\/shipment\/track.phtml\' shipment=$shipment order=$order":"Shipment Track Details","var formattedShippingAddress|raw":"Shipping Address","var order.shipping_description":"Shipping Description","var order.getShippingDescription()":"Shipping Description"}');
        $template->save();
    }

    private function createEmailOrderConfirmation()
    {
        $template = $this->templateFactory->create();
        $template->setTemplateCode('EN-Email Order confirmation');
        $template->setTemplateText('<div style=\'font:11px/1.35em Verdana, Arial, Helvetica, sans-serif;\'>
	<table cellspacing=\'0\' cellpadding=\'0\' border=\'0\' width=\'98%\' style=\'margin-top:10px; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; margin-bottom:10px;\'>
		<tr>
			<td align=\'center\' valign=\'top\'>
				<table bgcolor=\'FFFFFF\' cellspacing=\'0\' cellpadding=\'10\' border=\'0\' width=\'582\'>
					<!-- [ header starts here] -->
					<tr style=\'margin-bottom:10px;\'>
						<td valign=\'top\' style=\'text-align: center;\'>
							<a href=\'{{store url=\'\'}}\'><img src=\'{{media url=\'email/logo/default/email_header.png\' }}\' alt=\'{{var logo_alt}}\' border=\'0\'/></a>
						</td>
					</tr>
					<!-- [ middle starts here] -->
					<tr>
						<td valign=\'top\'>
							<p style=\'line-height:22px;font-size:16px\'><strong>Thank you {{trans "%customer_name," customer_name=$order.getCustomerName()}} for your purchase on CAFE.NET.VN,</strong></p>
							<p style=\'line-height:22px;\'>CAFE.NET.VN is plesant to annouce that your order {{trans \'<span class="no-link">#%increment_id</span>\' increment_id=$order.increment_id |raw}} has been confirmed and is in the process. CAFE.NET.VN will notify you as soon as the goods are ready for delivery.</p>
							<p style=\'line-height:22px;\'>*Note: To ensure you will receive the right package, please receive items only when the order status is updated to <strong>"On delivery"</strong></p>
						    <p style=\'line-height:22px;font-size:14px\'>The order shipped to</p>
                             <p>{{var formattedShippingAddress|raw}}</p>
                               {{layout handle="sales_email_order_items" order=$order area="frontend"}}
                                 <p>Payment method</p>
                        {{var payment_html|raw}}
         <p>Shipping options</h3>
                        <p>{{var order.getShippingDescription()}}</p>
                        {{if shipping_msg}}
                        <p>{{var shipping_msg}}</p>
                        {{/if}}
                            <p style=\'line-height:22px;\'><strong>Want to know exactly when to receive your order?</strong></p>
                            <p style=\'line-height:22px;\'>Before delivery, the shipping unit will contact you to schedule a delivery time!</p>
                           <p style=\'line-height:22px;font-size:15px\'>HOTLINE: 19006016</p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>');
        $template->setTemplateStyles('body,td { color:#2f2f2f; font:11px/1.35em Verdana, Arial, Helvetica, sans-serif; }');
        $template->setTemplateType(2);
        $template->setTemplateSubject('CAFE.NET.VN - Order confirmation');
        $template->setOrigTemplateCode('sales_email_order_template');
        $template->setOrigTemplateVariables('{"var formattedBillingAddress|raw":"Billing Address","var order.getEmailCustomerNote()":"Email Order Note","var order.increment_id":"Order Id","layout handle=\"sales_email_order_items\" order=$order area=\"frontend\"":"Order Items Grid","var payment_html|raw":"Payment Details","var formattedShippingAddress|raw":"Shipping Address","var order.getShippingDescription()":"Shipping Description","var shipping_msg":"Shipping message"}');
        $template->save();
    }
}

