<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$showOnDesktop = esc_attr(get_option('wpsaio_show_on_desktop', 1));

$showOnMobile = esc_attr(get_option('wpsaio_show_on_mobile', 1));

$displayCondition = esc_attr(get_option('wpsaio_display_condition', 'allPages'));

$includePages = get_option('wpsaio_includes_pages', []);

$excludePages = get_option('wpsaio_excludes_pages', []);

$getPagesQuery = new \WP_Query(array("posts_per_page" => -1, "post_type" => "page", "post_status" => "publish"));

?>
<div class="wrap-content-box">
    <table class="form-table">
        <p><?php echo esc_html__('Setting text and style for the floating widget.', 'support-chat') ?></p>
        <tbody>
            <tr>
                <th scope="row"><label for="wpsaio-show-desktop-switch"><?php echo esc_html__('Show on desktop', 'support-chat') ?></label></th>
                <td>
                    <div class="wpsaio-switch-control">
                        <input type="checkbox" id="wpsaio-show-desktop-switch" value="1" name="showOnDesktop" <?php checked($showOnDesktop, 1) ?>>
                        <label for="wpsaio-show-desktop-switch" class="green"></label>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wpsaio-show-mobile-switch"><?php echo esc_html__('Show on mobile', 'support-chat') ?></label></th>
                <td>
                    <div class="wpsaio-switch-control">
                        <input type="checkbox" id="wpsaio-show-mobile-switch" value="1" name="showOnMobile" <?php checked($showOnMobile, 1) ?>>
                        <label for="wpsaio-show-mobile-switch" class="green"></label>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="displayCondition"><?php echo esc_html__('Display', 'support-chat') ?></label></th>
                <td>
                    <select name="displayCondition" id="displayCondition">
                        <option <?php selected($displayCondition, 'allPages'); ?> value="allPages"><?php echo esc_html__("Show on all pages", 'support-chat') ?></option>
                        <option <?php selected($displayCondition, 'includePages'); ?> value="includePages"><?php echo esc_html__("Show on these pages...", 'support-chat') ?></option>
                        <option <?php selected($displayCondition, 'excludePages'); ?> value="excludePages"><?php echo esc_html__("Hide on these pages...", 'support-chat') ?></option>
                    </select>
                    <!-- <p class="description"><?php //esc_html_e("Please select 'Show on all pages except' if you want to display the widget on WooCommerce pages.", 'support-chat') 
                                                ?></p> -->
                </td>
            </tr>
            <th scope="row">
                <!-- <label for="widget_show_on_pages">
                <?php // echo esc_html__('Select pages', 'support-chat') 
                ?>
            </label> -->
            </th>
            <td class="nta-wa-pages-content include-pages <?php echo esc_attr($displayCondition == 'includePages' ? '' : 'hide-select') ?>">
                <input type="checkbox" id="include-pages-checkall" />
                <label for="include-pages-checkall">All</label>
                <ul id="nta-wa-display-pages-list">
                    <?php
                    $array_includes = $includePages;
                    if (!$array_includes) {
                        $array_includes = array();
                    }
                    while ($getPagesQuery->have_posts()) : $getPagesQuery->the_post();
                    ?>
                        <li>
                            <input <?php if (in_array(get_the_ID(), $array_includes)) {
                                        echo 'checked="checked"';
                                    } ?> name="includePages[]" class="includePages" type="checkbox" value="<?php esc_attr(the_ID()) ?>" id="nta-wa-hide-page-<?php esc_attr(the_ID()) ?>" />
                            <label for="nta-wa-hide-page-<?php esc_attr(the_ID()) ?>"><?php esc_html(the_title()) ?></label>
                        </li>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </ul>
            </td>

            <td class="nta-wa-pages-content exclude-pages <?php echo esc_attr($displayCondition == 'excludePages' ? '' : 'hide-select') ?>">
                <input type="checkbox" id="exclude-pages-checkall" />
                <label for="exclude-pages-checkall">All</label>
                <ul id="nta-wa-display-pages-list">
                    <?php
                    $array_excludes = $excludePages;
                    if (!$array_excludes) {
                        $array_excludes = array();
                    }
                    while ($getPagesQuery->have_posts()) : $getPagesQuery->the_post();
                    ?>
                        <li>
                            <input <?php if (in_array(get_the_ID(), $array_excludes)) {
                                        echo 'checked="checked"';
                                    } ?> name="excludePages[]" class="excludePages" type="checkbox" value="<?php esc_attr(the_ID()) ?>" id="nta-wa-show-page-<?php esc_attr(the_ID()) ?>" />
                            <label for="nta-wa-show-page-<?php esc_attr(the_ID()) ?>"><?php esc_html(the_title()) ?></label>
                        </li>
                    <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </ul>
            </td>
            </tr>
        </tbody>
    </table>
    <div class="wp_saio_panel_btn-wrap">
        <button class="wpsaio-save button button-primary button-display-settings"><?php echo esc_html__('Save Changes', 'support-chat') ?><i class="dashicons dashicons-update-alt"></i></button>
    </div>
</div>