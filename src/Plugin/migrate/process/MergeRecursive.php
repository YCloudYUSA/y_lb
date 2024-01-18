<?php

namespace Drupal\y_lb\Plugin\migrate\process;

use Drupal\Component\Utility\NestedArray;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Fills in Layout Builder sections.
 *
 * @MigrateProcessPlugin(
 *   id = "merge_recursive"
 * )
 */
class MergeRecursive extends ProcessPluginBase {
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!is_array($value)) {
      throw new MigrateException(sprintf('Merge process failed for destination property (%s): input is not an array.', $destination_property));
    }
    $new_value = [];
    foreach ($value as $i => $item) {
      if (!is_array($item)) {
        throw new MigrateException(sprintf('Merge process failed for destination property (%s): index (%s) in the source value is not an array that can be merged.', $destination_property, $i));
      }
      $new_value[] = $item;
    }

    return NestedArray::mergeDeep(...$new_value);
  }
}
