<?php
/**
 * Plugin Name:       Goal Progress Tracker
 * Description:       A beautiful goal progress tracker that displays progress as a horizontal thermometer with customizable gradient colors.
 * Version:           0.1.1
 * Requires at least: 6.9
 * Requires PHP:      8.0
 * Author:            Nate Finch
 * Author URI:        https://n8finch.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       goal-progress-tracker
 *
 * @package GoalProgress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function goal_progress_block_init() {
	register_block_type( __DIR__ . '/build/' );
}
add_action( 'init', 'goal_progress_block_init' );
