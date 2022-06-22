<?php
/**
 * Cấu hình cơ bản cho WordPress
 *
 * Trong quá trình cài đặt, file "wp-config.php" sẽ được tạo dựa trên nội dung 
 * mẫu của file này. Bạn không bắt buộc phải sử dụng giao diện web để cài đặt, 
 * chỉ cần lưu file này lại với tên "wp-config.php" và điền các thông tin cần thiết.
 *
 * File này chứa các thiết lập sau:
 *
 * * Thiết lập MySQL
 * * Các khóa bí mật
 * * Tiền tố cho các bảng database
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** Thiết lập MySQL - Bạn có thể lấy các thông tin này từ host/server ** //
/** Tên database MySQL */
define( 'DB_NAME', 'kangaroo_wp' );

/** Username của database */
define( 'DB_USER', 'root' );

/** Mật khẩu của database */
define( 'DB_PASSWORD', '1' );

/** Hostname của database */
define( 'DB_HOST', 'localhost' );

/** Database charset sử dụng để tạo bảng database. */
define( 'DB_CHARSET', 'utf8mb4' );

/** Kiểu database collate. Đừng thay đổi nếu không hiểu rõ. */
define('DB_COLLATE', '');

/**#@+
 * Khóa xác thực và salt.
 *
 * Thay đổi các giá trị dưới đây thành các khóa không trùng nhau!
 * Bạn có thể tạo ra các khóa này bằng công cụ
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * Bạn có thể thay đổi chúng bất cứ lúc nào để vô hiệu hóa tất cả
 * các cookie hiện có. Điều này sẽ buộc tất cả người dùng phải đăng nhập lại.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'L?OTSTLm y#f8D_Sm^*mM?@GNW`Y}!<1rC-zDLOc>7vcm_#Y![*bwYsUv-CT+q]Q' );
define( 'SECURE_AUTH_KEY',  'qV<GYIG+$%hsunO,^.xGv>b4!+*1TH]#wOs~e*r,@(t`z@vW*NkhX|Vm3iv[)7rL' );
define( 'LOGGED_IN_KEY',    'K)i^n>v})R-E^&w!,%!20|71gA8HRR^XioY%xh2X+N/tP!y|h0Fm[B/@:DgFvv7K' );
define( 'NONCE_KEY',        'ntr_5VLC)ZWB|/ FH~NuJL}J<Ae4t#L.8MUBuA7&|:x^0}{->du;r8Fd]o:Z)0_M' );
define( 'AUTH_SALT',        'pW)A,a?V?OrTs}5<rU?89}H~ZuIzVD=0bV;V[_I1*A<?C-jqyTd uSYZ7K*S0 @1' );
define( 'SECURE_AUTH_SALT', 't.hNSS?Yoq{LprpEgfuT#75[ l@X=KXuR9UR0 [HJWNH>@:EOy`&aQF:_THlr)6V' );
define( 'LOGGED_IN_SALT',   'NO~HN<=p|JD]+ui6R}Xw|Y>55WJOk%u~,hc;.}#_~[,%T |eX!q&r/Y;]*QWT|sf' );
define( 'NONCE_SALT',       'p;Q+bQ(,X; l)hmGA/D8zwy]zr.1akb(NZPp5VPy(U%+^mkk4FopiNNr(vrjkRt=' );

/**#@-*/

/**
 * Tiền tố cho bảng database.
 *
 * Đặt tiền tố cho bảng giúp bạn có thể cài nhiều site WordPress vào cùng một database.
 * Chỉ sử dụng số, ký tự và dấu gạch dưới!
 */
$table_prefix = 'kgr_wp_';

/**
 * Dành cho developer: Chế độ debug.
 *
 * Thay đổi hằng số này thành true sẽ làm hiện lên các thông báo trong quá trình phát triển.
 * Chúng tôi khuyến cáo các developer sử dụng WP_DEBUG trong quá trình phát triển plugin và theme.
 *
 * Để có thông tin về các hằng số khác có thể sử dụng khi debug, hãy xem tại Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* Đó là tất cả thiết lập, ngưng sửa từ phần này trở xuống. Chúc bạn viết blog vui vẻ. */

/** Đường dẫn tuyệt đối đến thư mục cài đặt WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Thiết lập biến và include file. */
require_once(ABSPATH . 'wp-settings.php');
