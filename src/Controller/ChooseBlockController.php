<?php

namespace Drupal\y_lb\Controller;

use Drupal\layout_builder\Controller\ChooseBlockController as ChooseBlockControllerCore;
use Drupal\layout_builder\SectionStorageInterface;

/**
 * Defines a controller to choose a new block.
 *
 * @internal
 *   Controller classes are internal.
 */
class ChooseBlockController extends ChooseBlockControllerCore {

  /**
   * {@inheritdoc}
   */
  public function build(SectionStorageInterface $section_storage, $delta, $region) {
    $build = parent::build($section_storage, $delta, $region);

    $build['system'] = [
      '#type' => 'details',
      '#title' => $this->t('All system blocks'),
      '#open' => FALSE,
    ];

    $build['system']['filter'] = $build['filter'];
    $build['system']['block_categories'] = $build['block_categories'];
    unset($build['filter']);
    unset($build['block_categories']);

    return $build;
  }

}
