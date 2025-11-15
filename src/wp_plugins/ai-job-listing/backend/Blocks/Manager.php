<?php
namespace Axilweb\AiJobListing\Blocks;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Block Manager class.
 *
 * Responsible for managing all the blocks.
 */
class Manager {

	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
		add_action( 'init', [ $this, 'register_all_blocks' ] );
	}

	/**
	 * Registers all blocks and handles necessary compatibility checks for WordPress versions.
	 *
	 * This method first checks if the current WordPress version is below 6.0. If it is, it applies a filter
	 * to the `plugins_url` to allow linked block assets from themes. Afterward, it registers the block metadata.
	 * Once the blocks are registered, it removes the `plugins_url` filter if the WordPress version is pre-6.0.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */

	public function register_all_blocks(): void {
		global $wp_version;

		$is_pre_wp_6 = version_compare( $wp_version, '6.0', '<' );

		if ( $is_pre_wp_6 ) {
			// Filter the plugins URL to allow us to have blocks in themes with linked assets.
			add_filter( 'plugins_url', [ $this, 'filter_plugins_url' ], 10, 2 );
		}

        $this->register_block_metas();

		if ( $is_pre_wp_6 ) {
			// Remove the filter after we register the blocks
			remove_filter( 'plugins_url', [ $this, 'filter_plugins_url' ], 10, 2 );
		}
	}

    /**
	 * Registers block metadata and render callback for the blocks.
	 *
	 * This method registers the block types for the AI Job Listing plugin by iterating over the specified block slugs.
	 * For each block, it checks if a corresponding markup file exists. If the file is present, it registers a render 
	 * callback function that will be used to dynamically generate the block's HTML output. The method ensures that the 
	 * blocks are rendered using their respective templates.
	 *
	 * @return void
	 *
	 * @since 0.1.0
	 */ 
    protected function register_block_metas(): void {
        $blocks = [  
            'ai-job-listing/',
        ];

        foreach ( $blocks as $block ) {
            $block_folder    = AXILWEB_AJL_PATH . '/build/blocks/' . $block;
            $block_options   = [];
            $markup_file_path = AXILWEB_AJL_TEMPLATE_PATH . '/blocks/' . $block . 'markup.php';

            if ( file_exists( $markup_file_path ) ) {
                $block_options['render_callback'] = function( $attributes, $content, $block ) use ( $markup_file_path ) {
                    $context = $block->context;
                    ob_start();
                    include $markup_file_path;
                    return ob_get_clean();
                };
            }

            register_block_type_from_metadata( $block_folder, $block_options );
        }
    }

	/**
	 * Filter the plugins_url to allow us to use assets from theme.
     *
     * @since 1.0.0
	 *
	 * @param string $url  The plugins url
	 * @param string $path The path to the asset.
	 *
	 * @return string The overridden url to the block asset.
	 */
	public function filter_plugins_url( string $url, string $path ): string {
		$file = preg_replace( '/\.\.\//', '', $path );
		return trailingslashit( get_stylesheet_directory_uri() ) . $file;
	}
}
