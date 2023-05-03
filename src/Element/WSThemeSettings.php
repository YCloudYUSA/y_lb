<?php

namespace Drupal\y_lb\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\Core\Render\Element\FormElementInterface;
use Drupal\Core\Render\Markup;

/**
 * Provides a form element for Website Services global style setup.
 *
 * @FormElement("ws_style_select")
 */
class WSThemeSettings extends FormElement implements FormElementInterface {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = static::class;
    return [
      '#input' => TRUE,
      '#multiple' => FALSE,
      '#default_value' => NULL,
      '#process' => [
        [$class, 'processWSThemeSettings'],
      ],
      '#theme_wrappers' => ['container'],
      '#value_callback' => [
        [$class, 'valueCallback'],
      ],
      '#component' => NULL,
    ];
  }

  /**
   * Processes a ws_style_select element.
   *
   * @param array $element
   *   An associative array containing the properties of the element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @return array
   *   The processed element.
   */
  public static function processWSThemeSettings(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#tree'] = TRUE;
    $ws_style = \Drupal::service('plugin.manager.ws_style');
    $ws_style_options = \Drupal::service('plugin.manager.ws_style_option');

    // Build controls for styles.
    $ws_styles = !empty($element['#component']) ? $ws_style->getStyleForComponent($element['#component']) : $ws_style->getGlobalStyles();
    foreach ($ws_styles as $name => $label) {
      $options = $ws_style_options->getStylesByGroup($name);
      // Skip if no options are available for the global styles group.
      if (!$options) {
        continue;
      }
      // Get options markup.
      foreach ($options as $option_key => $option) {
        $options[$option_key] = static::getOptionLabelMarkup($option);
      }
      // Build main radio element.
      $element[$name] = [
        '#type' => 'radios',
        '#title' => $label,
        '#default_value' => $element['#default_value'][$name],
        '#options' => $options,
      ];
    }

    return $element;
  }

  /**
   * Get Style Option markup for label.
   *
   * @param array $option
   *   Option data.
   *
   * @return string
   *   Markup value.
   */
  public static function getOptionLabelMarkup(array $option): string {
    $markup = '';
    if (!empty($option['image'])) {
      $markup = Markup::create('<img src="' . $option['image'] . '" alt="' . $option['label'] . '">');
    }

    return $markup ?: $option['label'];
  }

  /**
   * {@inheritdoc}
   */
  public static function valueCallback(&$element, $input, FormStateInterface $form_state) {
    return $input;
  }

}
