<?php
/**
 * Models
 *
 * @package Models
 */

namespace LassoLite\Classes;

use LassoLite\Admin\Constant;
use LassoLite\Classes\Amazon_Api;
use LassoLite\Classes\Meta_Enum;
use LassoLite\Models\Model;
use WP_Query;

/**
 * Model
 */
class SURL extends LL_Object {
	/**
	 * Get permalink
	 */
	public function get_permalink() {
		return esc_url( get_permalink( $this->get_id() ) );
	}

	/**
	 * Get edit URL
	 */
	public function get_link_detail() {
		return get_edit_post_link( $this->get_id() );
	}

	/**
	 * Get thumbnail URL
	 */
	public function get_thumbnail_url() {
		$img_src = get_post_meta( $this->get_id(), Meta_Enum::LASSO_LITE_CUSTOM_THUMBNAIL, true );

		return ( ! empty( $img_src ) ) ? $img_src : Constant::DEFAULT_THUMBNAIL;
	}

	/**
	 * Get redirect URL
	 */
	public function get_redirect_url() {
		$redirect = get_post_meta( $this->get_id(), Meta_Enum::SURL_REDIRECT, true );
		$redirect = Helper::has_protocol( $redirect ) ? $redirect : '#';

		if ( Amazon_Api::is_amazon_url( $redirect ) ) {
			$redirect = Amazon_Api::get_amazon_product_url( $redirect );
		}

		return $redirect;
	}

	/**
	 * Get public URL
	 */
	public function get_public_url() {
		$redirect = $this->get_redirect_url();
		if ( Amazon_Api::is_amazon_url( $redirect ) ) {
			return $redirect;
		}

		return $this->get_permalink();
	}

	/**
	 * Get clicks
	 */
	public function get_clicks() {
		$clicks = get_post_meta( $this->get_id(), Meta_Enum::SURL_COUNT, true );
		$clicks = ! empty( $clicks ) ? $clicks : 0;

		return $clicks;
	}

	/**
	 * Get list SURL object
	 *
	 * @param string $keyword Keyword.
	 * @param int    $page    Page name.
	 * @param int    $limit   Limit items.
	 * @return SURL[]
	 */
	public static function get_list( $keyword = '', $page = 1, $limit = Enum::LIMIT_ON_PAGE ) {
		if ( 1 === $page ) {
			$offset = 0;
		} else {
			$offset = ( $page * $limit ) - $limit;
		}
		$surls = get_posts(
			array(
				'posts_per_page' => $limit,
				'offset'         => $offset,
				'post_type'      => SIMPLE_URLS_SLUG,
				'post_status'    => 'publish',
				's'              => $keyword,
				'orderby'        => 'post_modified',
				'order'          => 'DESC',
			)
		);
		$list  = array();
		foreach ( $surls as $surl ) {
			$surl->id = $surl->ID;
			$list[]   = new self( $surl );
		}
		return $list;
	}

	/**
	 * Get total
	 *
	 * @param string $keyword Keyword.
	 * @return integer
	 */
	public static function total( $keyword = '' ) {
		if ( empty( $keyword ) ) {
			return (int) wp_count_posts( SIMPLE_URLS_SLUG )->publish;
		}
		$args      = array(
			'post_status' => 'publish',
			'post_type'   => SIMPLE_URLS_SLUG,
			's'           => $keyword,
		);
		$the_query = new WP_Query( $args );
		return $the_query->post_count;
	}

	/**
	 * Get urls by group
	 *
	 * @param Group $group Group object.
	 *
	 * @return array
	 */
	public static function get_urls_by_group( $group ) {
		$terms              = Model::get_wpdb()->terms;
		$term_taxonomy      = Model::get_wpdb()->term_taxonomy;
		$term_relationships = Model::get_wpdb()->term_relationships;
		$posts              = Model::get_wpdb()->posts;

		$sql = '
			SELECT
				t.term_id,
				t.name as term_name,
				t.slug as term_slug,
				t.term_group,
				p.ID,
				p.post_title,
				p.post_name
			FROM
					' . $terms . ' as t
				INNER JOIN
					' . $term_taxonomy . " as tt
						ON t.term_id = tt.term_id
						AND tt.taxonomy = '" . Constant::LASSO_CATEGORY . "'
				LEFT JOIN
					" . $term_relationships . ' as tr
						ON tt.term_taxonomy_id = tr.term_taxonomy_id
				INNER JOIN
					' . $posts . " as p
						ON tr.object_id = p.ID
			WHERE
				t.term_id = %d
				AND t.slug = %s 
				AND p.post_type = '" . Constant::LASSO_POST_TYPE . "' 
				AND p.post_status = 'publish' 
				AND tt.taxonomy = '" . Constant::LASSO_CATEGORY . "'";
		$sql = Model::prepare( $sql, $group->get_id(), $group->get_slug() );

		$results = Model::get_results( $sql );
		$list    = array();
		foreach ( $results as $result ) {
			$result->id = $result->ID;
			$list[]     = new self( $result );
		}
		return $list;
	}
}
