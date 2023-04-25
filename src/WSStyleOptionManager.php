<?php

namespace Drupal\y_lb;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;


/**
 * Manages discovery and instantiation of style plugins.
 *
 * @see plugin_api
 */
class WSStyleOptionManager extends DefaultPluginManager implements WSStyleOptionInterface {

  /**
   * Constructs a new WSStyleOptionManager object.
   *
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   */
  public function __construct(ModuleHandlerInterface $module_handler, CacheBackendInterface $cache_backend) {
    $this->moduleHandler = $module_handler;
    $this->setCacheBackend($cache_backend, 'ws_style_option', ['ws_style_option']);
    $this->alterInfo('ws_style_option');
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!isset($this->discovery)) {
      $this->discovery = new YamlDiscovery('ws_style_option', $this->moduleHandler->getModuleDirectories());
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($this->discovery);
    }
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getStylesByGroup(string $group): array {
    $options = [];

    foreach ($this->getDefinitions() as $module_options) {
      $module_options = array_filter($module_options, 'is_int', ARRAY_FILTER_USE_KEY);
      foreach ($module_options as $option) {
        if ($option['group'] == $group) {
          $options[$option['name']] = $option['label'];
        }
      }
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(string $group, string $style): array {
    $libraries = [];

    foreach ($this->getDefinitions() as $module_options) {
      $module_options = array_filter($module_options, 'is_int', ARRAY_FILTER_USE_KEY);
      foreach ($module_options as $style_option) {
        if ($style_option['group'] == $group
          && $style_option['name'] == $style) {
          $libraries[] = $style_option['library'];
        }
      }
    }

    return $libraries;
  }

}
