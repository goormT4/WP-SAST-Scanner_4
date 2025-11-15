<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$enablePlugin = esc_attr(get_option('wpsaio_enable_plugin', 1));

$widgetPosition = esc_attr(get_option('wpsaio_widget_position', 'right'));

$style = esc_attr(get_option('wpsaio_style', 'redirect'));

$tooltip = esc_attr(get_option('wpsaio_tooltip', 'appname'));

$bottomDistance = esc_attr(get_option('wpsaio_bottom_distance', 30));

$buttonIcon = esc_attr(get_option('wpsaio_button_icon'));

$buttonImage = esc_attr(get_option('wpsaio_button_image', 'contain'));

$buttonColor = esc_attr(get_option('wpsaio_button_color'));

?>

<div class="wrap-content-box">
    <p><?php echo esc_html__('Setting style for the floating widget.', 'support-chat') ?></p>
    <form action="options.php" method="post">
        <?php settings_fields('wpsaio'); ?>
        <?php do_settings_sections('wpsaio'); ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wpsaio-enable-plugin-switch"><?php esc_html_e('Enable plugin', 'support-chat'); ?></label></th>
                <td>
                    <div class="wpsaio-switch-control">
                        <input type="checkbox" name="wpsaio_enable_plugin" value="1" id="wpsaio-enable-plugin-switch" class="" <?php echo checked($enablePlugin, 1) ?> />
                        <label for="wpsaio-enable-plugin-switch" class="green"></label>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaioWidgetPosition"><?php echo esc_html__('Widget position', 'support-chat') ?></label></th>
                <td>
                    <div class="setting align">
                        <div class="button-group button-large" data-setting="align">
                            <button class="button btn-widget-position btn-left <?php echo $widgetPosition == 'left' ? 'active' : '' ?>" value="left" type="button">
                                <?php echo esc_html__('Left', 'support-chat') ?>
                            </button>
                            <button class="button btn-widget-position btn-right <?php echo $widgetPosition == 'right' ? 'active' : '' ?>" value="right" type="button">
                                <?php echo esc_html__('Right', 'support-chat') ?>
                            </button>
                        </div>
                        <input name="wpsaio_widget_position" id="wpsaioWidgetPosition" class="hidden" value="<?php echo esc_attr($widgetPosition); ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaioStyle"><?php esc_html_e('Style', 'support-chat'); ?></label></th>
                <td>
                    <div class="setting align">
                        <div class="button-group button-large" data-setting="align">
                            <button class="button btn-style btn-redirect <?php echo $style == 'redirect' ? 'active' : '' ?>" value="redirect" type="button">
                                <?php echo esc_html__('Redirect', 'support-chat') ?>
                            </button>
                            <button class="button btn-style btn-popup <?php echo $style == 'popup' ? 'active' : '' ?>" value="popup" type="button">
                                <?php echo esc_html__('Popup', 'support-chat') ?>
                            </button>
                        </div>
                        <input name="wpsaio_style" id="wpsaioStyle" class="hidden" value="<?php echo esc_attr($style); ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaioTooltip"><?php esc_html_e('Tooltip', 'support-chat'); ?></label></th>
                <td>
                    <div class="setting align">
                        <div class="button-group button-large" data-setting="align">
                            <button class="button btn-tooltip btn-appname <?php echo $tooltip == 'appname' ? 'active' : '' ?>" value="appname" type="button">
                                <?php echo esc_html__('App Name', 'support-chat') ?>
                            </button>
                            <button class="button btn-tooltip btn-appcontent <?php echo $tooltip == 'appcontent' ? 'active' : ''  ?>" value="appcontent" type="button">
                                <?php echo esc_html__('App Content', 'support-chat') ?>
                            </button>
                        </div>
                        <input name="wpsaio_tooltip" id="wpsaioTooltip" class="hidden" value="<?php echo esc_attr($tooltip); ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaio_bottom_distance"><?php esc_html_e('Padding from bottom', 'support-chat'); ?></label></th>
                <td>
                    <div class="range" style='--min:0; --max:500; --value:<?php echo esc_attr(get_option('wpsaio_bottom_distance', 30)); ?>; --text-value:"<?php echo esc_attr($bottomDistance); ?>";'>
                        <input id="wpsaio_bottom_distance" name="wpsaio_bottom_distance" type="range" min="0" max="500" value="<?php echo esc_attr($bottomDistance); ?>" oninput="this.parentNode.style.setProperty('--value',this.value); this.parentNode.style.setProperty('--text-value', JSON.stringify(this.value))">
                        <output></output>
                        <div class='range__progress'></div>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaio_button_icon"><?php esc_html_e('Custom icon/avatar', 'support-chat'); ?></label></th>
                <td>
                    <input type="text" name="wpsaio_button_icon" id="wpsaio_button_icon" value="<?php echo esc_attr($buttonIcon); ?>" class="regular-text" />
                    <span class="button wp_saio_choose_image_btn" data-target="#wpsaio_button_icon"><?php esc_html_e('Choose Image', 'support-chat'); ?></span>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaioButtonImage"><?php esc_html_e('Button style', 'support-chat'); ?></label></th>
                <td>
                    <div class="setting align">
                        <div class="button-group button-large" data-setting="align">
                            <button class="button btn-button-image btn-contain <?php echo $buttonImage == 'contain' ? 'active' : ''; ?>" value="contain" type="button">
                                <?php echo esc_html__('Contain', 'support-chat') ?>
                            </button>
                            <button class="button btn-button-image btn-cover <?php echo $buttonImage == 'cover' ? 'active' : ''; ?>" value="cover" type="button">
                                <?php echo esc_html__('Cover', 'support-chat') ?>
                            </button>
                        </div>
                        <input name="wpsaio_button_image" id="wpsaioButtonImage" class="hidden" value="<?php echo esc_attr($buttonImage); ?>" />
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaio_button_color"><?php esc_html_e('Button color', 'support-chat'); ?></label></th>
                <td>
                    <input type="text" name="wpsaio_button_color" value="<?php echo esc_attr($buttonColor); ?>" id="wpsaio_button_color" class="regular-text wp_saio_colorpicker" />
                </td>
            </tr>
        </table>
        <div class="wp_saio_panel_btn-wrap">
            <button class="wpsaio-save button button-primary button-design-settings"><?php echo esc_html__('Save Changes', 'support-chat') ?><i class="dashicons dashicons-update-alt"></i></button>
        </div>
    </form>
</div>
