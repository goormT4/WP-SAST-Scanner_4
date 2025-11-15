<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap-content-box">
    <h1>Settings</h1>
    <?php settings_errors(); ?>
    <div id="tabs">
        <ul class="nav-tab-wrapper nta-tab-wrapper">
            <li><a href="#tabs-1" class="nav-tab nta-design-tab nav-tab-active" data-action="njt_wpsaio_save_design_setting"><?php echo esc_html__('Design', 'support-chat') ?></a></li>
            <li><a href="#tabs-2" class="nav-tab nta-display-setting-tab" data-action="njt_wpsaio_save_display_setting"><?php echo esc_html__('Display Settings', 'support-chat') ?></a></li>
        </ul>
        <div class="nta-tabs-content">
            <form method="post" action="options.php">
                <div id="form-selected-account" autocomplete="off">
                    <div id="tabs-1">
                        <?php require_once WP_SAIO_DIR . '/views/admin/design-settings.php' ?>
                    </div>
                    <div id="tabs-2" style="display: none;">
                        <?php require_once WP_SAIO_DIR . '/views/admin/display-settings.php' ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>