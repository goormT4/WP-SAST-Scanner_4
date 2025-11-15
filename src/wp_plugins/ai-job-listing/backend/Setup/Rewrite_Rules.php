<?php
namespace Axilweb\AiJobListing\Setup; 
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}
class Rewrite_Rules 
{
  const PREFIX = 'axilweb_ajl_'; 
  /**
   * Constructor method for the Rewrite_Rules class.
   *
   * @since 1.0.0
   */
  public function __construct()
    {
      add_filter( 'query_vars', array( $this, 'renderJobListingQueryVars' ) );
      add_action( 'generate_rewrite_rules', array( $this, 'addRewrite_RulesForJobListingPage' ) );
      add_filter( 'the_content', array( $this, 'renderJobListingPage' ) ); 
      add_action( 'admin_init', array( $this, 'ai_job_listing_check_plain_permalink')); 
    } 

  /**
   * Check if permalink structure is set to "Plain" and show an admin notice.
   *
   * This function checks if the permalink structure is set to "Plain" (empty value)
   * and displays an admin notice to inform the user about the potential issues
   * with job listings.
   *
   * @since 1.0.0
   *
   * @return void
   */
  public function ai_job_listing_check_plain_permalink() {
    if (get_option('permalink_structure') === '') { // "Plain" structure has an empty value
        add_action('admin_notices', array($this, 'ai_job_listing_plain_permalink_notice'));
    }
  }

  /**
   * Display admin notice for "Plain" permalinks.
   *
   * This function displays an admin notice to inform the user about the potential
   * issues with job listings when the permalink structure is set to "Plain".
   *
   * @since 1.0.0
   *
   * @return void
   */
  public function ai_job_listing_plain_permalink_notice() {
    ?>
    <div class="notice notice-warning">
    <p>
              <span class="dashicons dashicons-megaphone" style="color: #d63638; font-size: 20px; vertical-align: middle; margin-right: 5px;"></span>
              <strong><?php echo esc_html__('AI Job Listing Plugin:', 'ai-job-listing'); ?></strong> 
              <?php echo esc_html__('Your permalink structure is set to "Plain", which may cause issues with job listings.', 'ai-job-listing'); ?>
              <?php echo esc_html__('Please change it to "Post name" in', 'ai-job-listing'); ?>
              <a href="<?php echo esc_url(admin_url('options-permalink.php')); ?>" target="_blank">
                  <?php echo esc_html__('Settings → Permalinks', 'ai-job-listing'); ?>
              </a> 
              <?php echo esc_html__('for proper functionality.', 'ai-job-listing'); ?>
          </p>
    </div>
    <?php
  }

    
  /**
   * Add custom query variables for job listings.
   *
   * This function adds the custom query variable `ai_job_listing_segments` to the list of public query variables.
   * This variable can be used to capture additional segments in the URL when querying job listings.
   *
   * @since 1.0.0
   *
   * @param array $vars The array of current query variables.
   * @return array The modified array of query variables, including the custom job listing segment.
  */
  public function renderJobListingQueryVars( $vars ) {
    $vars[] = 'ai_job_listing_segments'; 
    return $vars;
  }

  /**
   * Add custom rewrite rules for the job listing page.
   *
   * This function defines a custom rewrite rule for the job listing page using the page slug and segments.
   * The custom rule allows the job listing page to accept dynamic URL segments and pass them as query variables.
   *
   * @since 1.0.0
   *
   * @param WP_Rewrite $wpjob_list_rewrite The WordPress rewrite object for managing rewrite rules.
   * @return void
   */
  public function addRewrite_RulesForJobListingPage( $wpjob_list_rewrite ) {
      $page_id = (int) get_option(self::PREFIX . 'career_page');  
      $page_slug = get_post_field( 'post_name', $page_id ); 
      $new_rules[ "({$page_slug})/(.+?)/?$" ] = 'index.php?pagename=' . $wpjob_list_rewrite->preg_index( 1 ) . '&ai_job_listing_segments=' . $wpjob_list_rewrite->preg_index( 2 );
      $wpjob_list_rewrite->rules = $new_rules + $wpjob_list_rewrite->rules; 
  }
  
  /**
   * Render the job listing page with custom content.
   *
   * This function replaces the default content of the job listing page with a custom HTML container and updates the page.
   * It injects a `div` with a base permalink attribute for use in JavaScript applications.
   *
   * @since 1.0.0
   *
   * @param string $contents The original content of the job listing page.
   * @return string The modified content containing the custom HTML structure.
   */
  public function renderJobListingPage($contents)
  {
      $page_id = (int) get_option(self::PREFIX . 'career_page');
    
      // Check if the current page is the career page
      if (get_the_ID() !== $page_id) {
          return $contents;
      }
      // Retrieve the base path for the page
      $base_path = get_post_field('post_name', $page_id);
      // Ensure the page ID is set and matches the current page
      if (!$base_path) {
          return $contents;
      }

      // Updated content with sanitized and escaped values
      $updated_content = '<div 
          id="' . esc_attr('ai-job-listing-career') . '" 
          data-base-permalink="' . esc_attr($base_path) . '"></div>';

      // Update the post content securely
      wp_update_post([
          'ID'           => $page_id,
          'post_content' => $updated_content,
      ]);

      return $updated_content;
  }

}
