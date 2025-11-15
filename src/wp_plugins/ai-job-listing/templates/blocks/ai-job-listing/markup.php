<?php
/**
 * Example block markup
 *
 * @var array    $attributes         Block attributes.
 * @var string   $content            Block content.
 * @var WP_Block $block              Block instance.
 * @var array    $context            Block context.
 */
if (!defined('ABSPATH')) {
    exit;
}
?>

<p>
    <?php
        $job_data = Axilweb\AiJobListing\Helpers\Helpers::gutenberg($attributes);
        if ($job_data) {
            echo wp_kses_post($job_data);
        }
    ?>
</p>