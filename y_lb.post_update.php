<?php

/**
 * @file
 * Post update functions for the y_lb module.
 */

/**
 * Fix inline_block_usage for all Layout Builder nodes.
 *
 * When nodes with Layout Builder sections are created via migrations,
 * the inline_block_usage table is not populated. This causes access
 * issues when editing inline blocks with Media Library.
 */
function y_lb_post_update_fix_inline_block_usage(&$sandbox) {
  $database = \Drupal::database();
  $entity_type_manager = \Drupal::entityTypeManager();
  $node_storage = $entity_type_manager->getStorage('node');

  // Initialize batch.
  if (!isset($sandbox['progress'])) {
    $sandbox['progress'] = 0;
    $sandbox['current_nid'] = 0;
    $sandbox['fixed_count'] = 0;

    // Count total nodes with layout_builder__layout field.
    $query = $node_storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('nid', 0, '>');
    $sandbox['max'] = $query->count()->execute();

    if ($sandbox['max'] == 0) {
      $sandbox['#finished'] = 1;
      return t('No nodes to process.');
    }
  }

  // Process 50 nodes per batch.
  $batch_size = 50;
  $nids = $node_storage->getQuery()
    ->accessCheck(FALSE)
    ->condition('nid', $sandbox['current_nid'], '>')
    ->sort('nid')
    ->range(0, $batch_size)
    ->execute();

  if (empty($nids)) {
    $sandbox['#finished'] = 1;
    return t('Fixed @count inline_block_usage records.', ['@count' => $sandbox['fixed_count']]);
  }

  $nodes = $node_storage->loadMultiple($nids);

  foreach ($nodes as $node) {
    $sandbox['progress']++;
    $sandbox['current_nid'] = $node->id();

    if (!$node->hasField('layout_builder__layout')) {
      continue;
    }

    $sections = $node->get('layout_builder__layout')->getValue();
    foreach ($sections as $section_data) {
      if (empty($section_data['section'])) {
        continue;
      }
      /** @var \Drupal\layout_builder\Section $section */
      $section = $section_data['section'];
      $components = $section->getComponents();

      foreach ($components as $component) {
        $config = $component->get('configuration');
        if (empty($config['id']) || strpos($config['id'], 'inline_block:') !== 0) {
          continue;
        }

        $block_revision_id = $config['block_revision_id'] ?? NULL;
        if (!$block_revision_id) {
          continue;
        }

        $block_content = $entity_type_manager
          ->getStorage('block_content')
          ->loadRevision($block_revision_id);

        if (!$block_content) {
          continue;
        }

        $existing = $database->select('inline_block_usage', 'ibu')
          ->fields('ibu')
          ->condition('block_content_id', $block_content->id())
          ->execute()
          ->fetchAssoc();

        if ($existing) {
          continue;
        }

        $database->insert('inline_block_usage')
          ->fields([
            'block_content_id' => $block_content->id(),
            'layout_entity_type' => 'node',
            'layout_entity_id' => $node->id(),
          ])
          ->execute();
        $sandbox['fixed_count']++;
      }
    }
  }

  $sandbox['#finished'] = $sandbox['progress'] / $sandbox['max'];

  if ($sandbox['#finished'] >= 1) {
    return t('Fixed @count inline_block_usage records.', ['@count' => $sandbox['fixed_count']]);
  }
}
