<?php
/**
 * Plugin Name: Facebook and Instagram Embeds Audit
 * Description: A series of WP-CLI tools to help you find Facebook and Instagram embeds in your content.
 * Version: 0.1
 * Author URI: http://www.alleyinteractive.com/

 * @package FB_IG_Embeds_Audit
 */

namespace FB_IG_Embeds_Audit;

if ( ! class_exists( '\WP_CLI' ) ) {
	return;
}

require_once __DIR__ . '/trait-alley-cli-bulk-task.php';

/**
 * Class Command.
 */
class Command extends \WP_CLI_Command {
	use CLI_Bulk_Task;

	/**
	 * Get all http references in post_content.
	 *
	 * ## OPTIONS
	 *
	 * [--post_id]
	 * : A specific post ID to find embeds within.
	 *
	 * [--format=<format>]
	 * : Render output in a particular format.
	 * ---
	 * default: table
	 * options:
	 *   - table
	 *   - csv
	 *   - json
	 *   - count
	 *   - yaml
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp fb-ig-embeds-audit list
	 *     wp fb-ig-embeds-audit list --post_id=123
	 *     wp fb-ig-embeds-audit list --format=json
	 *
	 * @param array $args The command args.
	 * @param array $assoc_args The command associated  args.
	 */
	public function list( $args, $assoc_args ) {
		$assoc_args = wp_parse_args(
			$assoc_args,
			[
				'format' => 'table',
			]
		);

		// Search all posts.
		if ( empty( $assoc_args['post_id'] ) ) {
			$data = $this->get_all_embeds();
		} else {
			$data = $this->get_embeds_from_post( absint( $assoc_args['post_id'] ) );
		}

		\WP_CLI::line( sprintf( 'Formatting %s results', number_format( count( $data ) ) ) );
		\WP_CLI\Utils\format_items( $assoc_args['format'], $data, [ 'ID', 'URL', 'Edit URL', 'Snippet' ] );

		\WP_CLI::line( sprintf( 'Found %s embeds', number_format( count( $data ) ) ) );
	}

	/**
	 * Get all posts with references to Facebook or Instagram oEmbeds.
	 *
	 * @return array {
	 *     Search results data.
	 *
	 *     @type int $ID The post ID.
	 *     @type string $URL The URL to view the post.
	 *     @type string ${Edit URL} The URL to edit the post.
	 *     @type string $Snippet The matching content, in context.
	 * }
	 */
	protected function get_all_embeds() {
		$data = [];
		$admin_url = admin_url( 'post.php?post=%d&action=edit' );

		\WP_CLI::line( 'Analyzing content...' );
		$this->bulk_task(
			function( $post ) use ( &$data, $admin_url ) {
				$embeds = $this->get_embeds( $post->post_content );

				// Skip if this post has no embeds.
				if ( empty( $embeds ) ) {
					return;
				}

				$data_row = [
					'ID'       => $post->ID,
					'URL'      => get_permalink( $post ),
					'Edit URL' => sprintf( $admin_url, $post->ID ),
				];

				foreach ( $embeds as $snippet ) {
					$data_row['Snippet'] = $snippet;
					$data[] = $data_row;
				}
			}
		);

		return $data;
	}

	/**
	 * Get all posts with references to Facebook or Instagram oEmbeds.
	 *
	 * @param int $post_id The post ID.
	 * @return array {
	 *     Search results data.
	 *
	 *     @type int $ID The post ID.
	 *     @type string $URL The URL to view the post.
	 *     @type string ${Edit URL} The URL to edit the post.
	 *     @type string $Snippet The matching content, in context.
	 * }
	 */
	protected function get_embeds_from_post( $post_id ) {
		$data = [];
		$admin_url = admin_url( 'post.php?post=%d&action=edit' );

		$post = get_post( $post_id );

		// Invalid post object.
		if ( ! $post instanceof \WP_Post ) {
			return [];
		}

		\WP_CLI::line( 'Analyzing content...' );

		$embeds = $this->get_embeds( $post->post_content );

		// Skip if this post has no embeds.
		if ( empty( $embeds ) ) {
			return;
		}

		$data_row = [
			'ID'       => $post->ID,
			'URL'      => get_permalink( $post ),
			'Edit URL' => sprintf( $admin_url, $post->ID ),
		];

		foreach ( $embeds as $snippet ) {
			$data_row['Snippet'] = $snippet;
			$data[] = $data_row;
		}

		return $data;
	}

	/**
	 * Gets all FB|IG embeds given an HTML string.
	 *
	 * @param  string $post_content The post content to check.
	 * @return array Array of embeds.
	 */
	protected function get_embeds( $post_content ) {
		$post_content = wp_strip_all_tags( $post_content );

		$pattern      = get_shortcode_regex();
		$post_content = preg_replace(
			"/$pattern/",
			'${5}',
			$post_content
		);

		$patterns = [
			// Facebook.
			'#https?://www\.facebook\.com/.*/posts/.*#i',
			'#https?://www\.facebook\.com/.*/activity/.*#i',
			'#https?://www\.facebook\.com/.*/photos/.*#i',
			'#https?://www\.facebook\.com/photo\.php.*#i',
			'#https?://www\.facebook\.com/permalink\.php?story_fbid=.*#i',
			'#https?://www\.facebook\.com/media/set?set=.*#i',
			'#https?://www\.facebook\.com/questions/.*#i',
			'#https?://www\.facebook\.com/notes/.*#i',
			'#https?://www\.facebook\.com/.*/videos/.*#i',
			'#https?://www\.facebook\.com/video\.php.*#i',
			// Instagram.
			'#https?://(www\.)?instagr(\.am|am\.com)/(p|tv)/.*#i',
		];

		$embeds = [];

		foreach ( $patterns as $pattern ) {
			preg_match_all( $pattern, $post_content, $matches );

			$matches = array_filter( $matches );

			if ( empty( $matches ) ) {
				continue;
			}

			foreach ( $matches[0] as $match ) {
				$embeds[] = $match;
			}
		}

		return $embeds;
	}
}

\WP_CLI::add_command( 'fb-ig-embeds-audit', __NAMESPACE__ . '\Command' );
