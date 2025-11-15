<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$is_mobile = wp_is_mobile();

$from_db = get_option('njt_wp_saio', null);

$tooltip_type = get_option('wpsaio_tooltip', 'appname');

if($app === 'line') {
    //for websites that use http instead of https
    $content = str_replace('http://', 'https://', $content);
}

if ($is_mobile && $app === 'line') {
    $lineUrl = $from_db['line']['params']['url'];
?>
    <div class="nt-aio-item js__nt_aio_item" data-target="#nt-aio-popup-<?php echo esc_attr($app); ?>" data-is-mobile="<?php echo esc_attr($is_mobile); ?>">
        <a href="<?php echo esc_url( WpSaioHelper::format_content($app, $content, true) ); ?>" target="_blank" class='line-mobile-link'></a>
        <div class="nt-aio-item-icon nt-aio-<?php echo esc_attr( $app ); ?>"></div>

        <!-- /.nt-aio-item-icon nt-aio- -->
        <div class="nt-aio-item-txt"><?php echo $tooltip_type === "appname" ? esc_html( $title ) : esc_html( $content ); ?></div>
        <!-- /.nt-aio-item-txt -->
        </a>
    </div>
<?php
} else if ($app === 'custom-app') { ?>
      <div class="nt-aio-item js__nt_aio_item" data-target="#nt-aio-popup-<?php echo esc_attr($app); ?>" data-is-mobile="<?php echo esc_attr($is_mobile); ?>">
        <div class="nt-aio-item-icon nt-aio-<?php echo esc_attr($app); ?>" data-appname='<?php echo esc_attr($app); ?>' data-coloricon='<?php echo esc_attr($args['color-icon']); ?>' data-urlicon='<?php echo esc_attr($args['url-icon']); ?>'></div>
        <!-- /.nt-aio-item-icon nt-aio- -->
        <div class="nt-aio-item-txt" data-title="<?php echo $args['custom-app-title'] !== '' ? esc_attr($args['custom-app-title']) : esc_attr($title) ?>" data-content="<?php echo esc_attr($content); ?>">
            <?php echo $tooltip_type === "appname" ? ($args['custom-app-title'] !== '' ? esc_html($args['custom-app-title']) : esc_html($title)) : esc_html($content); ?>
        </div>
        <!-- /.nt-aio-item-txt -->
    </div>
<?php } else { ?>

    <div class="nt-aio-item js__nt_aio_item" data-target="#nt-aio-popup-<?php echo esc_attr($app); ?>" data-is-mobile='<?php echo esc_attr($is_mobile); ?>'>
        <div class="nt-aio-item-icon nt-aio-<?php echo esc_attr($app); ?>" data-appname='<?php echo esc_attr($app); ?>' data-coloricon='<?php echo esc_attr($args['color-icon']); ?>' data-urlicon='<?php echo esc_attr($args['url-icon']); ?>'></div>
        <!-- /.nt-aio-item-icon nt-aio- -->
        <div class="nt-aio-item-txt" data-app="<?php echo esc_attr($app); ?>" data-title="<?php echo esc_attr($title); ?>" data-content="<?php echo esc_attr(WpSaioHelper::format_content($app, $content, true)); ?>">
            <?php echo $tooltip_type === "appname" ? esc_html($title) : esc_html($content); ?>
        </div>
        <!-- /.nt-aio-item-txt -->
    </div>
<?php }
?>