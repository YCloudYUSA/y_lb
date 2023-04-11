<?php

namespace Drupal\y_lb\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Render\Element\FormElementInterface;

/**
 * Provides a form element for Website Services global theme setup.
 *
 * @FormElement("ws_theme_select")
 */
class YThemeSettings extends FormElement implements FormElementInterface {

  public function getInfo() {
    $class = static::class;
    return [
      '#input' => TRUE,
      '#multiple' => FALSE,
      '#default_value' => NULL,
      '#process' => [
        [$class, 'processYThemeSettings'],
      ],
      '#theme_wrappers' => ['container'],
      '#value_callback' => [
        [$class, 'valueCallback'],
      ],
    ];
  }

  public static function processYThemeSettings(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#tree'] = TRUE;
    $element['color_scheme_select'] = [
      '#type' => 'radios',
      '#title' => 'Select color scheme',
      '#default_value' => $element['#value']['color_scheme_select'],
      '#options' => [
        'Blue - green',
        'Red - yellow',
        'Blue - white'
      ]
    ];

    $element['button_styles_select'] = [
      '#type' => 'radios',
      '#title' => 'Button styles',
      '#default_value' => $element['#value']['button_styles_select'],
      '#options' => [
        'Solid',
        'Outlined'
      ]
    ];

    return $element;
  }

  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    return $input;
  }

}
