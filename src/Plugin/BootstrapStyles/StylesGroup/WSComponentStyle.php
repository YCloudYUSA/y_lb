<?php

namespace Drupal\y_lb\Plugin\BootstrapStyles\StylesGroup;

use Drupal\bootstrap_styles\StylesGroup\StylesGroupPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class WSComponentStyle.
 *
 * @package Drupal\bootstrap_styles\Plugin\StylesGroup
 *
 * @StylesGroup(
 *   id = "ws_style",
 *   title = @Translation("Y Style"),
 *   weight = 0,
 *   icon = "y_lb/assets/svg/y.svg"
 * )
 */
class WSComponentStyle extends StylesGroupPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['ws_style'] = [
      '#type' => 'details',
      '#title' => $this->t('Y Style'),
      '#open' => FALSE,
    ];

    return $form;
  }

}
