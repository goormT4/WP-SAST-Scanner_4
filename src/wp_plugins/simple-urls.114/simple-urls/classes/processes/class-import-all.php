<?php
/**
 * Declare class Lasso_Process_Import_All
 *
 * @package Lasso_Process_Import_All
 */

namespace LassoLite\Classes\Processes;

use LassoLite\Classes\Helper as Lasso_Helper;
use LassoLite\Classes\Import as Lasso_Import;
use LassoLite\Classes\Lasso_DB;

use LassoLite\Classes\Processes\Process;

use LassoLite\Models\Model;

/**
 * Lasso_Process_Import_All
 */
class Import_All extends Process {
	const LIMIT  = 500;
	const OPTION = 'lasso_lite_import_all_enable';

	/**
	 * Action name
	 *
	 * @var string $action
	 */
	protected $action = 'lassolite_import_process_all';

	/**
	 * Log name
	 *
	 * @var string $log_name
	 */
	protected $log_name = 'lite_bulk_import';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $data Queue item to iterate over.
	 *
	 * @return mixed
	 */
	public function task( $data ) {
		$time_start       = microtime( true );
		$import_id        = $data['import_id'] ?? false;
		$post_type        = $data['post_type'] ?? false;
		$post_title       = $data['post_title'] ?? '';
		$import_permalink = $data['import_permalink'] ?? '';

		$lasso_import = new Lasso_Import();
		if ( $import_id && $post_type ) {
			$lasso_import->process_single_link_data( $import_id, $post_type, $post_title, $import_permalink );
		}

		$this->set_processing_runtime();
		$time_end       = microtime( true );
		$execution_time = round( $time_end - $time_start, 2 );

		return false;
	}

	/**
	 * Prepare data for process
	 *
	 * @param string $filter_plugin Plugin name.
	 */
	public function import( $filter_plugin = null ) {
		// ? check whether process is age out and make it can work on Lasso UI via ajax requests
		$this->is_process_age_out();

		if ( $this->is_process_running() ) {
			return false;
		}

		$lasso_db = new Lasso_DB();

		$sql         = $lasso_db->get_importable_urls_query( false, '', '', $filter_plugin );
		$sql         = $lasso_db->paginate( $sql, 1, self::LIMIT );
		$all_imports = Model::get_results( $sql );
		$count       = count( $all_imports );

		if ( $count <= 0 ) {
			update_option( self::OPTION, '0' );
			return false;
		}
		update_option( self::OPTION, '1' );

		foreach ( $all_imports as $import ) {
			$import = Lasso_Helper::format_importable_data( $import );
			if ( empty( $import->id ) || empty( $import->post_type ) || 'checked' === $import->check_status ) {
				continue;
			}

			$this->push_to_queue(
				array(
					'import_id'        => $import->id,
					'post_type'        => $import->post_type,
					'post_title'       => Lasso_Helper::remove_unexpected_characters_from_post_title( $import->post_title ),
					'import_permalink' => $import->import_permalink,
				)
			);
		}

		$this->set_total( $count );
		$this->set_log_file_name( $this->log_name );
		$this->task_start_log();
		// ? save queue
		$this->save()->dispatch();

		return true;
	}
}
