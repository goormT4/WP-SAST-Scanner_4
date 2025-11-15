<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class WpSaioShortcodes {

	private static $_instance = null;

	public function __construct() {
		// Register shortcodes will be done in init hook to avoid early translation calls
		add_action( 'init', array( $this, 'registerShortcodes' ) );
	}

	public function registerShortcodes() {
		$apps = WpSaio::defaultApps();
		foreach ( $apps as $k => $v ) {
			if ( isset( $v['shortcode'] ) && ! empty( $v['shortcode'] ) ) {
				$func = str_replace( '-', '', $k );
				if(str_contains($k, 'custom-app')) {
					add_shortcode( $v['shortcode'], array( $this,'customAppShortcode' ) );
				} else {
					add_shortcode( $v['shortcode'], array( $this, $func . 'Shortcode' ) );
				}
			}
		}
	}
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	public function facebookmessengerShortcode( $atts ) {
		ob_start();
		$atts     = shortcode_atts(
			array(
				'url' => '',
			),
			$atts
		);
		$atts     = extract( $atts );
		$urlArray = explode( '/', $url );
		?>
		<div class="nt-aio-popup nt-aio-messenger-popup" id="nt-aio-popup-facebook-messenger">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Messenger', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href='<?php echo esc_attr( WpSaioHelper::format_content('messenger', $url, true) ); ?>'></a>
				<iframe wh-src="fbIframeURL" style="border:none; border-radius: 0 0 16px 16px; overflow:hidden" scrolling="no" allowtransparency="true" src="https://www.facebook.com/plugins/page.php?href=<?php echo esc_url( WpSaioHelper::format_content('messenger', $url, true) ); ?>&amp;tabs=messages&amp;small_header=true&amp;width=300&amp;height=300&amp;adapt_container_width=true&amp;hide_cover=true&amp;show_facepile=false&amp;appId" width="300" height="300" frameborder="0"></iframe>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<!-- /#nt-aio-popup-facebook-messenger.nt-aio-popup nt-aio-messenger-popup -->
		<?php
		return ob_get_clean();
	}
	public function whatsappShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'phone' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		// preg_match_all( '/\d+/', $phone, $matches );
		// $phone = isset($matches[0][0]) ? $matches[0][0] : '';
		?>
		<div class="nt-aio-popup nt-aio-whatsapp-popup" id="nt-aio-popup-whatsapp">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'WhatsApp', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="https://api.whatsapp.com/send?phone=<?php echo esc_attr( WpSaioHelper::format_content('whatsapp', $phone, false) ); ?>" target="_blank">
					<?php echo esc_attr( WpSaioHelper::format_content('whatsapp', $phone, false) ); ?>
				</a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<!-- /#nt-aio-popup-whatsapp.nt-aio-popup nt-aio-whatsapp-popup -->
		<?php
		return ob_get_clean();
	}
	public function snapchatShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'username' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-snapchat-popup" id="nt-aio-popup-snapchat">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Snapchat', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<div class="content-snapchat-qrcode" wh-html="snapchatQRCode"><object data="https://feelinsonice-hrd.appspot.com/web/deeplink/snapcode?username=<?php echo esc_attr($username); ?>&amp;type=PNG" type="image/png" width="200px" height="200px"></object></div>
				<div style="margin: 5px;" class="content-snapchat-name" wh-html-unsafe="snapchatUser">
					<a href="<?php echo esc_attr(WpSaioHelper::format_content('snapchat', $username, true)); ?>" target="_blank"><?php echo esc_html(WpSaioHelper::format_content('snapchat', $username, false)); ?></a>
				</div>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<!-- /#nt-aio-popup-snapchat.nt-aio-popup nt-aio-snapchat-popup -->
			<?php
			return ob_get_clean();
	}
	public function lineShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'url' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		//for websites that use http instead of https
		$url = str_replace('http://', 'https://', $url);
		?>
		<div class="nt-aio-popup nt-aio-line-popup" id="nt-aio-popup-line">
			<!-- <p class="test">Test</p> -->
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Line', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<?php if( ! empty( $url ) ) : ?>
				<iframe wh-src="lineIframeURL" scrolling="no" allowtransparency="true" src="<?php echo esc_url( WpSaioHelper::format_content('line', $url, true) ); ?>" frameborder="0"></iframe>
				<a href="https://line.me/R/ti/p/@<?php echo esc_url( WpSaioHelper::format_content('line', $url, false) ); ?>" target="_blank"></a>
				<?php endif; ?>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}
	public function viberShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'account' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-viber-popup" id="nt-aio-popup-viber">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Viber', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="<?php echo esc_attr( WpSaioHelper::format_content('viber', $account, true) ); ?>" target="_blank"><?php echo esc_attr( WpSaioHelper::format_content('viber', $account, false) ); ?></a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}
	public function phoneShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'phone_number' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-phone-popup" id="nt-aio-popup-phone">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Phone', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="<?php echo esc_attr(WpSaioHelper::format_content('phone', $phone_number, true)); ?>"><?php echo esc_attr(WpSaioHelper::format_content('phone', $phone_number, false)); ?></a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}
	public function emailShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'email' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-email-popup" id="nt-aio-popup-email">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Email', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="<?php echo esc_attr(WpSaioHelper::format_content('email', $email, true)); ?>" target="_blank"><?php echo esc_attr(WpSaioHelper::format_content('email', $email, false)); ?></a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}

	public function telegramShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'username' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-telegram-popup" id="nt-aio-popup-telegram">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Telegram', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="<?php echo esc_attr(WpSaioHelper::format_content('telegram', $username, true)); ?>" target="_blank">
				<?php echo esc_attr(WpSaioHelper::format_content('telegram', $username, false)); ?>
				</a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}

	public function skypeShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'username' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-skype-popup" id="nt-aio-popup-skype">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Skype', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="skype:<?php echo esc_attr($username); ?>?chat" target="_blank">
				<?php echo esc_attr($username); ?>
				</a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}

	public function zaloShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'username' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-zalo-popup" id="nt-aio-popup-zalo">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Zalo', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="<?php echo esc_attr(WpSaioHelper::format_content('zalo', $username, true)); ?>" target="_blank">
				<?php echo esc_attr(WpSaioHelper::format_content('zalo', $username, false)); ?>
				</a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}

	public function kakaotalkShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'username' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-kakaotalk-popup" id="nt-aio-popup-kakaotalk">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Kakaotalk', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="kakaotalk:<?php echo esc_attr(WpSaioHelper::format_content('kakaotalk', $username, false)); ?>?chat" target="_blank">
				<?php echo esc_attr(WpSaioHelper::format_content('kakaotalk', $username, false)); ?>
				</a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}

	public function wechatShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'email' => '',
			),
			$atts
		);
		$atts = extract( $atts );
		?>
		<div class="nt-aio-popup nt-aio-wechat-popup" id="nt-aio-popup-wechat">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt"><?php esc_html_e( 'Wechat', 'support-chat' ); ?></div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="<?php echo esc_attr(WpSaioHelper::format_content('wechat', $email, true)); ?>" target="_blank">
					<?php echo esc_attr(WpSaioHelper::format_content('wechat', $email, false)); ?>
				</a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<?php
		return ob_get_clean();
	}

	public function customAppShortcode( $atts ) {
		ob_start();
		$atts = shortcode_atts(
			array(
				'url'              => '',
				'custom-app-title' => '',
				'url-icon'         => '',
				'color-icon'       => '',
				'custom-app-key'   => '',
			),
			$atts
		);
		// $atts = extract($atts);
		?>
		<div class="nt-aio-popup nt-aio-custom-app-popup" id="<?php echo !empty($atts['custom-app-key']) ? 'nt-aio-popup-' . esc_attr($atts['custom-app-key']) : 'nt-aio-popup-custom-app'; ?>">
			<div class="nt-aio-popup-header">
				<div class="nt-aio-popup-title">
					<div class="nt-aio-popup-title-icon"></div>
					<!-- /.nt-aio-popup-title-icon -->
					<div class="nt-aio-popup-title-txt">
						<?php
						$custom_app_title = ($atts['custom-app-title'] && ($atts['custom-app-title'] !== '')) ? $atts['custom-app-title'] : esc_html__('Custom App', 'support-chat');
						echo esc_html($custom_app_title);
					?>
					</div>
					<!-- /.nt-aio-popup-title-txt -->
				</div>
				<!-- /.nt-aio-popup-title -->
				<div class="nt-aio-popup-close js__nt_aio_close_popup"></div>
				<!-- /.nt-aio-popup-close -->
			</div>
			<!-- /.nt-aio-popup-header -->
			<div class="nt-aio-popup-content">
				<a href="<?php echo esc_url( $atts['url'] ); ?>" target="_blank">
				<?php echo esc_url( $atts['url'] ); ?>
				</a>
			</div>
			<!-- /.nt-aio-popup-content -->
		</div>
		<style>
			.nt-aio-popup-header {
				--backgroundColorCustomApp: <?php echo $atts['color-icon'] ? esc_attr( $atts['color-icon'] ) : '#007cc4'; ?>;
			}

			.nt-aio-popup-title-icon {
				--backgroundIconCustomApp: <?php echo $atts['url-icon'] ? 'url(' . esc_attr( $atts['url-icon'] ) . ')' : 'url("../images/custom-app.svg")'; ?>;
			}
		</style>
		<?php
		return ob_get_clean();
	}
}
