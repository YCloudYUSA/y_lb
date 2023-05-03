<?php

namespace Drupal\y_lb\Plugin\BootstrapStyles\Style;

use Drupal\bootstrap_styles\Style\StylePluginBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\y_lb\WSStyleOptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class WSComponentStyleOption.
 *
 * @package Drupal\bootstrap_styles\Plugin\Style
 *
 * @Style(
 *   id = "ws_style_option",
 *   title = @Translation("WS Style Option"),
 *   group_id = "ws_style",
 *   weight = 1
 * )
 */
class WSComponentStyleOption extends StylePluginBase {


  /**
   * WS Style Option manager.
   *
   * @var \Drupal\y_lb\WSStyleOptionInterface
   */
  protected $wsStyleOptionManager;

  /**
   * Constructs a StylePluginBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   * @param \Drupal\y_lb\WSStyleOptionInterface
   *   The WS Style Option manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory, WSStyleOptionInterface $ws_style_option_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $config_factory);
    $this->wsStyleOptionManager = $ws_style_option_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('plugin.manager.ws_style_option')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {

    $form['ws_style_option'] = [
      '#type' => 'ws_style_select',
      '#default_value' => $storage['ws_style_option'],
      '#component' => $form_state->getFormObject()->getCurrentComponent()->getPluginId(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    $storage = [
      'ws_style_option' => $group_elements['ws_style_option'],
    ];

    return $storage;
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build, array $storage, $theme_wrapper = NULL) {
    $classes = $libraries = [];
    $ws_styles = $storage['ws_style_option'];
    foreach ($ws_styles as $group => $style) {
      $style_option = $this->wsStyleOptionManager->getStyleOption($group, $style);
      // Get libraries for the component.
      if (!empty($style_option['library'])) {
        $libraries[] = $style_option['library'];
      }
      // Get classes to be attached for the component.
      if (!empty($style_option['class'])) {
        $classes[] = $style_option['class'];
      }
      // Define custom template parameter for next template processing.
      if (!empty($style_option['template'])) {
        $build['ws_template'] = $style_option['template'];
      }
    }

    // Add the classes to the build.
    $build = $this->addClassesToBuild($build, $classes, $theme_wrapper);

    // Add the libraries to the build.
    $build['#attached']['library'] = $libraries;

    return $build;
  }

}
