<?php
/**
 * @file
 * Hooks specific to the y_lb module.
 */

/**
 * Alter the Node Share Block links.
 *
 * @param array $links
 *   The associative array of links to different sharing web resources.
 */
function hook_lb_node_share_block_links_alter(&$links) {
  $links['facebook'] = 'https://www.facebook.com/sharer.php?u=';
}
