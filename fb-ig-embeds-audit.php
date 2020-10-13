<?php
/**
 * Plugin Name: Facebook and Instagram Embeds Audit
 * Description: A series of WP-CLI tools to help you find Facebook and Instagram embeds in your content.
 * Author:      Alley
 * Author URI:  https://alley.co
 * Version:     0.1
 *
 * @package FB_IG_Embeds_Audit
 */

namespace FB_IG_Embeds_Audit;

define( 'FB_IG_EMBEDS_AUDIT_PATH', dirname( __FILE__ ) );

// Load traits.
require_once FB_IG_EMBEDS_AUDIT_PATH . '/inc/trait-cli-bulk-task.php';

// Load classes.
require_once FB_IG_EMBEDS_AUDIT_PATH . '/inc/class-cli.php';
