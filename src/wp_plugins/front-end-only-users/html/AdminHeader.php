<?php

// Securely fetch and sanitize stored options
$full_version     = sanitize_text_field( get_option( 'EWD_FEUP_Full_Version', 'No' ) );
$trial_happening  = sanitize_text_field( get_option( 'EWD_FEUP_Trial_Happening', 'No' ) );
?>
<div class="wrap">
    <div class="Header">
        <h2><?php esc_html_e( 'Front End Only Users Settings', 'front-end-only-users' ); ?></h2>
    </div>

    <?php
    // Show the upgrade banner if not premium or if trial is active
    if ( 'Yes' !== $full_version || 'Yes' === $trial_happening ) :

    ?>
        <div class="ewd-feup-dashboard-new-upgrade-banner">
            <div class="ewd-feup-dashboard-banner-icon"></div>

            <div class="ewd-feup-dashboard-banner-buttons">
                <a
                    class="ewd-feup-dashboard-new-upgrade-button"
                    href="<?php echo esc_url( 'https://www.etoilewebdesign.com/plugins/front-end-only-users/#plugin-sales-plans' ); ?>"
                    target="_blank"
                >
                    <?php esc_html_e( 'UPGRADE NOW', 'front-end-only-users' ); ?>
                </a>
            </div>

            <div class="ewd-feup-dashboard-banner-text">

                <div class="ewd-feup-dashboard-banner-title">
                    <?php esc_html_e( 'GET FULL ACCESS WITH OUR PREMIUM VERSION', 'front-end-only-users' ); ?>
                </div>
                <div class="ewd-feup-dashboard-banner-brief">
                    <?php esc_html_e( 'Experience the user management and membership plugin that allows for front-end user registration and login', 'front-end-only-users' ); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div> <!-- .wrap -->
